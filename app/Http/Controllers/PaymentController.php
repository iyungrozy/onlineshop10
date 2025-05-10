<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Services\DigiflazzService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $digiflazzService;

    public function __construct(DigiflazzService $digiflazzService)
    {
        $this->digiflazzService = $digiflazzService;
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_email' => 'required|email',
            'customer_phone' => 'required',
            'payment_method' => 'required|in:bank_transfer,credit_card,e_wallet'
        ]);

        $product = Product::findOrFail($request->product_id);

        // Create transaction record
        $transaction = Transaction::create([
            'order_id' => 'TRX-' . Str::random(10),
            'product_id' => $product->id,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'amount' => $product->price,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'transaction_status' => 'pending'
        ]);

        // TODO: Implement payment gateway integration (Midtrans/Xendit)
        // For now, we'll just return the transaction details
        return response()->json([
            'status' => 'success',
            'message' => 'Transaction created successfully',
            'data' => [
                'order_id' => $transaction->order_id,
                'amount' => $transaction->amount,
                'payment_method' => $transaction->payment_method
            ]
        ]);
    }

    public function handleCallback(Request $request)
    {
        // TODO: Implement payment gateway callback handling
        // This will be implemented when we integrate with Midtrans/Xendit
        return response()->json(['status' => 'success']);
    }

    public function checkStatus($orderId)
    {
        $transaction = Transaction::where('order_id', $orderId)->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => [
                'order_id' => $transaction->order_id,
                'payment_status' => $transaction->payment_status,
                'transaction_status' => $transaction->transaction_status
            ]
        ]);
    }
}
