<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'product']);

        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('order_id', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_start') && $request->has('date_end')) {
            $query->whereBetween('created_at', [
                $request->date_start . ' 00:00:00',
                $request->date_end . ' 23:59:59'
            ]);
        }

        $transactions = $query->latest()->paginate(10);
        return view('admin.transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'product']);
        return view('admin.transactions.show', compact('transaction'));
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
