<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\DigiflazzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $digiflazzService;

    public function __construct(DigiflazzService $digiflazzService)
    {
        $this->digiflazzService = $digiflazzService;
    }

    public function index(Request $request)
    {
        $query = Product::query();

        // Filter berdasarkan pencarian
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan kategori
        if ($request->has('category') && $request->category !== '') {
            $query->where('category', $request->category);
        }

        // Filter berdasarkan status
        if ($request->has('status')) {
            $query->where('active', $request->status === 'active');
        }

        $products = $query->latest()->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:game,pulsa,pln',
            'brand' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        try {
            $product = Product::create([
                'name' => $validated['name'],
                'category' => $validated['category'],
                'brand' => $validated['brand'],
                'price' => $validated['price'],
                'active' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
                'product' => $product
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating product: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan produk'
            ], 500);
        }
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:game,pulsa,pln',
            'brand' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        try {
            $product->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diperbarui',
                'product' => $product
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating product: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui produk'
            ], 500);
        }
    }

    public function toggleStatus(Product $product)
    {
        try {
            $product->update(['active' => !$product->active]);

            return response()->json([
                'success' => true,
                'message' => 'Status produk berhasil diubah',
                'product' => $product
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling product status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status produk'
            ], 500);
        }
    }

    public function syncFromDigiflazz()
    {
        try {
            $products = $this->digiflazzService->getProducts();

            foreach ($products as $productData) {
                Product::updateOrCreate(
                    ['digiflazz_id' => $productData['buyer_sku_code']],
                    [
                        'name' => $productData['product_name'],
                        'category' => $this->mapCategory($productData['category']),
                        'brand' => $productData['brand'],
                        'price' => $productData['price'],
                        'active' => true,
                    ]
                );
            }

        return response()->json([
                'success' => true,
                'message' => 'Sinkronisasi produk berhasil'
            ]);
        } catch (\Exception $e) {
            Log::error('Error syncing products: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat sinkronisasi produk'
            ], 500);
        }
    }

    protected function mapCategory($digiflazzCategory)
    {
        $categoryMap = [
            'Games' => 'game',
            'Pulsa' => 'pulsa',
            'PLN' => 'pln',
        ];

        return $categoryMap[$digiflazzCategory] ?? 'game';
    }
}
