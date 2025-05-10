<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $totalUsers = User::where('role', 'user')->count();
            $totalProducts = Product::count();
            $totalTransactions = Transaction::count();
            $totalRevenue = Transaction::where('status', 'success')
                ->sum(DB::raw('CAST(total_amount AS DECIMAL(10,2))'));

            $recentTransactions = Transaction::with(['product', 'user'])
                ->latest()
                ->take(5)
                ->get();

            $topProducts = Product::withCount(['transactions' => function($query) {
                    $query->where('status', 'success');
                }])
                ->orderBy('transactions_count', 'desc')
                ->take(5)
                ->get();

            return view('admin.dashboard', compact(
                'totalUsers',
                'totalProducts',
                'totalTransactions',
                'totalRevenue',
                'recentTransactions',
                'topProducts'
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
