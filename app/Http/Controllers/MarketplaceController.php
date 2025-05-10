<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index()
    {
        $products = Product::where('active', true)
            ->latest()
            ->paginate(12);

        return view('welcome', compact('products'));
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $category = $request->input('category');
        $brand = $request->input('brand');

        $products = Product::where('active', true)
            ->when($query, function ($q) use ($query) {
                return $q->where('name', 'like', "%{$query}%");
            })
            ->when($category, function ($q) use ($category) {
                return $q->where('category', $category);
            })
            ->when($brand, function ($q) use ($brand) {
                return $q->where('brand', $brand);
            })
            ->latest()
            ->paginate(12);

        return view('welcome', compact('products'));
    }

    public function myTransactions()
    {
        $transactions = Transaction::where('user_id', auth()->id())
            ->with('product')
            ->latest()
            ->paginate(10);

        return view('transactions.index', compact('transactions'));
    }
}
