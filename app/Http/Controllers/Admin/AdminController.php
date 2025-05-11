<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get statistics
        $totalProducts = Product::count();
        $totalUsers = User::count();
        $totalTransactions = Transaction::count();
        $totalRevenue = Transaction::where('status', 'success')->sum('total_amount');

        // Get recent transactions
        $recentTransactions = Transaction::with(['user', 'product'])
            ->latest()
            ->take(5)
            ->get();

        // Get top selling products
        $topProducts = Product::withCount(['transactions' => function($query) {
                $query->where('status', 'success');
            }])
            ->orderBy('transactions_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalUsers',
            'totalTransactions',
            'totalRevenue',
            'recentTransactions',
            'topProducts'
        ));
    }
} 