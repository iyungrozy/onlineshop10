<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Services\DigiflazzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    protected $digiflazzService;

    public function __construct(DigiflazzService $digiflazzService)
    {
        $this->digiflazzService = $digiflazzService;
    }

    public function index()
    {
        $transactions = auth()->user()->transactions()
            ->with('product')
            ->latest()
            ->paginate(10);

        return view('transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        
        return view('transactions.show', compact('transaction'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'user_id' => 'required|string',
            'user_name' => 'required|string',
        ]);

        try {
            $product = Product::findOrFail($request->product_id);
            
            // Create transaction
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'order_id' => 'TRX-' . time(),
                'user_id_game' => $request->user_id,
                'user_name_game' => $request->user_name,
                'total_amount' => $product->price,
                'status' => 'pending',
            ]);

            // Process with Digiflazz
            $response = $this->digiflazzService->createTransaction([
                'buyer_sku_code' => $product->sku,
                'customer_no' => $request->user_id,
                'ref_id' => $transaction->order_id,
                'sign' => md5(config('services.digiflazz.username') . config('services.digiflazz.api_key') . $transaction->order_id),
            ]);

            if ($response['success']) {
                $transaction->update([
                    'status' => $response['data']['status'],
                    'sn' => $response['data']['sn'] ?? null,
                ]);

                return redirect()->route('transactions.show', $transaction)
                    ->with('success', 'Transaction created successfully!');
            } else {
                $transaction->update(['status' => 'failed']);
                throw new \Exception($response['message'] ?? 'Failed to process transaction');
            }
        } catch (\Exception $e) {
            Log::error('Transaction failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to process transaction. Please try again.');
        }
    }
} 