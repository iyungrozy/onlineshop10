<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Services\DigiflazzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    protected $digiflazzService;

    public function __construct(DigiflazzService $digiflazzService)
    {
        $this->digiflazzService = $digiflazzService;
    }

    public function index()
    {
        $products = Product::latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'image_url' => 'nullable|url',
            'sku' => 'required|string|unique:products',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive'
        ]);

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'image_url' => 'nullable|url',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive'
        ]);

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function toggleStatus(Product $product)
    {
        try {
            $product->update(['status' => $product->status === 'active' ? 'inactive' : 'active']);

            return response()->json([
                'success' => true,
                'message' => 'Product status updated successfully',
                'product' => $product
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating product status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product status'
            ], 500);
        }
    }

    public function syncFromDigiflazz()
    {
        try {
            $products = $this->digiflazzService->getProducts();

            foreach ($products as $productData) {
                Product::updateOrCreate(
                    ['sku' => $productData['buyer_sku_code']],
                    [
                        'name' => $productData['product_name'],
                        'category' => $this->mapCategory($productData['category']),
                        'price' => $productData['price'],
                        'status' => 'active',
                        'stock' => 999, // Default stock for digital products
                        'description' => $productData['description'] ?? null,
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Products synchronized successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error syncing products: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to synchronize products'
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
