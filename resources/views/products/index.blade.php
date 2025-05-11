@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Products</h1>
    <p class="mt-2 text-gray-600">Find and purchase your favorite game products</p>
</div>

<!-- Categories -->
<div class="mb-8">
    <div class="flex space-x-4 overflow-x-auto pb-2">
        <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-full {{ !request('category') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            All
        </a>
        @foreach(['game', 'pulsa', 'pln'] as $category)
            <a href="{{ route('products.index', ['category' => $category]) }}" 
               class="px-4 py-2 rounded-full {{ request('category') === $category ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                {{ ucfirst($category) }}
            </a>
        @endforeach
    </div>
</div>

<!-- Products Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @foreach($products as $product)
    <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200">
        <div class="aspect-w-16 aspect-h-9">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="object-cover w-full h-48">
        </div>
        <div class="p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                    {{ $product->category === 'game' ? 'bg-purple-100 text-purple-800' : 
                       ($product->category === 'pulsa' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                    {{ ucfirst($product->category) }}
                </span>
                <span class="text-sm text-gray-500">{{ $product->brand }}</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
            <div class="flex items-center justify-between">
                <span class="text-xl font-bold text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                <button onclick="buyProduct({{ $product->id }})" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    Buy Now
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Pagination -->
<div class="mt-8">
    {{ $products->links() }}
</div>

<!-- Buy Product Modal -->
<div id="buyModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Buy Product</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <i data-feather="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="buyForm" method="POST" action="{{ route('transactions.store') }}">
            @csrf
            <input type="hidden" name="product_id" id="productId">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Product Details</label>
                <div id="productDetails" class="text-sm text-gray-600"></div>
            </div>
            
            <div class="mb-4">
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">User ID</label>
                <input type="text" name="user_id" id="user_id" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="mb-4">
                <label for="user_name" class="block text-sm font-medium text-gray-700 mb-1">User Name</label>
                <input type="text" name="user_name" id="user_name" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="closeModal()"
                        class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Confirm Purchase
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function buyProduct(productId) {
    // Fetch product details
    fetch(`/api/products/${productId}`)
        .then(response => response.json())
        .then(product => {
            document.getElementById('productId').value = product.id;
            document.getElementById('productDetails').innerHTML = `
                <p><strong>Name:</strong> ${product.name}</p>
                <p><strong>Category:</strong> ${product.category}</p>
                <p><strong>Price:</strong> Rp ${new Intl.NumberFormat('id-ID').format(product.price)}</p>
            `;
            document.getElementById('buyModal').classList.remove('hidden');
            document.getElementById('buyModal').classList.add('flex');
        });
}

function closeModal() {
    document.getElementById('buyModal').classList.add('hidden');
    document.getElementById('buyModal').classList.remove('flex');
}

// Close modal when clicking outside
document.getElementById('buyModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endpush
@endsection 