@extends('layouts.app')

@section('title', 'Transaction Details')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Transaction Details</h1>
            <p class="mt-2 text-gray-600">Order #{{ $transaction->order_id }}</p>
        </div>
        <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Transactions
        </a>
    </div>
</div>

<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Transaction Information</h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">Details about your purchase.</p>
    </div>
    <div class="border-t border-gray-200">
        <dl>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Order ID</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">#{{ $transaction->order_id }}</dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Product</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <img class="h-10 w-10 rounded-full" src="{{ $transaction->product->image_url }}" alt="{{ $transaction->product->name }}">
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $transaction->product->name }}</div>
                            <div class="text-sm text-gray-500">{{ $transaction->product->category }}</div>
                        </div>
                    </div>
                </dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Amount</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $transaction->status === 'success' ? 'bg-green-100 text-green-800' : 
                           ($transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($transaction->status) }}
                    </span>
                </dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Date</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $transaction->created_at->format('d M Y H:i') }}</dd>
            </div>
            @if($transaction->status === 'success' && $transaction->response_data)
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Response Data</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <pre class="bg-gray-50 p-4 rounded-md overflow-x-auto">{{ json_encode($transaction->response_data, JSON_PRETTY_PRINT) }}</pre>
                </dd>
            </div>
            @endif
        </dl>
    </div>
</div>
@endsection 