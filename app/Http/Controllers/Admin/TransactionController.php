<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
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

    public function index(Request $request)
    {
        try {
            $query = Transaction::with(['user', 'product'])
                ->latest();

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_id', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $transactions = $query->paginate(10);

            return view('admin.transactions.index', compact('transactions'));
        } catch (\Exception $e) {
            Log::error('Error in TransactionController@index: ' . $e->getMessage());
            return $this->handleError($e, 'Failed to load transactions');
        }
    }

    public function show(Transaction $transaction)
    {
        try {
            $transaction->load(['user', 'product']);
            return view('admin.transactions.show', compact('transaction'));
        } catch (\Exception $e) {
            Log::error('Error in TransactionController@show: ' . $e->getMessage());
            return $this->handleError($e, 'Failed to load transaction details');
        }
    }

    public function update(Transaction $transaction)
    {
        try {
            if ($transaction->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending transactions can be processed');
            }

            $response = $this->digiflazzService->processTransaction($transaction);

            if ($response['success']) {
                $transaction->update([
                    'status' => 'success',
                    'response_data' => $response['data']
                ]);

                return redirect()->back()->with('success', 'Transaction processed successfully');
            } else {
                $transaction->update([
                    'status' => 'failed',
                    'response_data' => $response['data']
                ]);

                return redirect()->back()->with('error', 'Failed to process transaction: ' . $response['message']);
            }
        } catch (\Exception $e) {
            Log::error('Error in TransactionController@update: ' . $e->getMessage());
            return $this->handleError($e, 'Failed to process transaction');
        }
    }

    public function updateStatus(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,success,failed',
            'notes' => 'nullable|string|max:255',
        ]);

        $transaction->update($validated);

        if ($validated['status'] === 'success') {
            // Kirim notifikasi ke user
            // TODO: Implementasi notifikasi
        }

        return redirect()->route('admin.transactions.show', $transaction)
            ->with('success', 'Status transaksi berhasil diperbarui');
    }

    public function export(Request $request)
    {
        $query = Transaction::with(['user', 'product']);

        if ($request->has('date_start') && $request->has('date_end')) {
            $query->whereBetween('created_at', [
                $request->date_start . ' 00:00:00',
                $request->date_end . ' 23:59:59'
            ]);
        }

        $transactions = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="transactions.csv"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, [
                'Order ID',
                'Tanggal',
                'User',
                'Produk',
                'Total',
                'Status',
                'Catatan'
            ]);

            // Data
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->order_id,
                    $transaction->created_at->format('d/m/Y H:i'),
                    $transaction->user->name,
                    $transaction->product->name,
                    number_format($transaction->total_amount, 0, ',', '.'),
                    $transaction->status,
                    $transaction->notes
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
