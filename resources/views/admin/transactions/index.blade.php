@extends('layouts.admin')

@section('title', 'Transaction Management')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Transaction Management</h1>
            <p class="mt-2 text-gray-600">View and manage all transactions</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-6 border-b border-gray-200">
        <form action="{{ route('admin.transactions.index') }}" method="GET" class="flex gap-4">
            <div class="flex-1">
                <label for="search" class="sr-only">Search</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
                           placeholder="Search by order ID or user name">
                </div>
            </div>
            <div>
                <label for="status" class="sr-only">Status</label>
                <select name="status" id="status"
                        class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none sm:text-sm rounded-md">
                    <option value="">All Status</option>
                    <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($transactions as $transaction)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        #{{ $transaction->order_id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $transaction->user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $transaction->user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-10 w-10 rounded-full" src="{{ $transaction->product->image_url }}" alt="{{ $transaction->product->name }}">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $transaction->product->name }}</div>
                                <div class="text-sm text-gray-500">{{ $transaction->product->category }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $transaction->status === 'success' ? 'bg-green-100 text-green-800' : 
                               ($transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $transaction->created_at->format('d M Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.transactions.show', $transaction) }}" 
                           class="text-blue-600 hover:text-blue-900">View Details</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        No transactions found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t">
        {{ $transactions->links() }}
    </div>
</div>
@endsection 