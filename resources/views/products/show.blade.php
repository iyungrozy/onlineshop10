@extends('layouts.app')

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-x-8 lg:items-start">
            <!-- Image gallery -->
            <div class="flex flex-col-reverse">
                <div class="w-full aspect-w-1 aspect-h-1 rounded-lg overflow-hidden">
                    <img src="{{ $product->image_url ?? 'https://via.placeholder.com/600' }}" alt="{{ $product->name }}" class="w-full h-full object-center object-cover">
                </div>
            </div>

            <!-- Product info -->
            <div class="mt-10 px-4 sm:px-0 sm:mt-16 lg:mt-0">
                <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">{{ $product->name }}</h1>

                <div class="mt-3">
                    <h2 class="sr-only">Product information</h2>
                    <p class="text-3xl text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                </div>

                <div class="mt-6">
                    <h3 class="sr-only">Description</h3>
                    <div class="text-base text-gray-700 space-y-6">
                        <p>{{ $product->description }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="flex items-center">
                        <h3 class="text-sm text-gray-600">Brand:</h3>
                        <p class="ml-2 text-sm text-gray-900">{{ $product->brand }}</p>
                    </div>
                    <div class="flex items-center mt-2">
                        <h3 class="text-sm text-gray-600">Category:</h3>
                        <p class="ml-2 text-sm text-gray-900">{{ $product->category }}</p>
                    </div>
                </div>

                @auth
                    <div class="mt-10 flex sm:flex-col1">
                        <button type="button" class="max-w-xs flex-1 bg-indigo-600 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-full">
                            Beli Sekarang
                        </button>
                    </div>
                @else
                    <div class="mt-10 flex sm:flex-col1">
                        <a href="{{ route('login') }}" class="max-w-xs flex-1 bg-indigo-600 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-full">
                            Login untuk Membeli
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
