@extends('admin.layouts.app')
@section('title', 'Orders')
@section('content')

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
  <h1 class="text-2xl font-bold">Orders</h1>
  <a href="{{ route('admin.orders.export', request()->query()) }}" class="inline-block bg-gray-900 text-white text-sm px-4 py-2 rounded-lg hover:bg-gray-800">⬇ Export CSV</a>
</div>

<form method="GET" class="bg-white rounded-2xl shadow p-4 mb-6 grid grid-cols-1 md:grid-cols-6 gap-3">
  <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search name, phone, address, order ID"
         class="md:col-span-2 px-3 py-2 border rounded-lg text-sm">
  <select name="status" class="px-3 py-2 border rounded-lg text-sm">
    <option value="">All Statuses</option>
    @foreach($statuses as $status)
      <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ $status }}</option>
    @endforeach
  </select>
  <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="px-3 py-2 border rounded-lg text-sm">
  <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="px-3 py-2 border rounded-lg text-sm">
  <select name="sort" class="px-3 py-2 border rounded-lg text-sm">
    <option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>Latest first</option>
    <option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>Oldest first</option>
  </select>
  <div class="md:col-span-6 flex gap-2">
    <button class="bg-gray-900 text-white text-sm px-4 py-2 rounded-lg hover:bg-gray-800">Filter</button>
    <a href="{{ route('admin.orders.index') }}" class="text-sm px-4 py-2 rounded-lg border hover:bg-gray-50">Reset</a>
  </div>
</form>

<div class="bg-white rounded-2xl shadow overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 text-gray-500 text-left">
        <tr>
          <th class="px-4 py-3">Order ID</th>
          <th class="px-4 py-3">Customer</th>
          <th class="px-4 py-3">Phone</th>
          <th class="px-4 py-3">Qty</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3">Time</th>
          <th class="px-4 py-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($orders as $order)
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 font-medium"><a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600">{{ $order->order_code }}</a></td>
            <td class="px-4 py-3">{{ $order->customer_name }}</td>
            <td class="px-4 py-3">{{ $order->phone }}</td>
            <td class="px-4 py-3">{{ $order->quantity }}</td>
            <td class="px-4 py-3">
              <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                @csrf @method('PATCH')
                <select name="status" onchange="this.form.submit()"
                        class="text-xs font-medium rounded-full px-2 py-1 border-0 {{ $order->statusBadgeClass() }}">
                  @foreach($statuses as $status)
                    <option value="{{ $status }}" @selected($order->status === $status)>{{ $status }}</option>
                  @endforeach
                </select>
              </form>
            </td>
            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $order->created_at->format('d M, h:i A') }}</td>
            <td class="px-4 py-3">
              <div class="flex justify-end gap-2 whitespace-nowrap">
                <a href="tel:{{ $order->phone }}" class="text-gray-500 hover:text-gray-900" title="Call">📞</a>
                <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:underline">View</a>
                <a href="{{ route('admin.orders.edit', $order) }}" class="text-gray-600 hover:underline">Edit</a>
                @if(auth()->user()->isAdmin())
                  <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" onsubmit="return confirm('Delete this order permanently?');">
                    @csrf @method('DELETE')
                    <button class="text-red-600 hover:underline">Delete</button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">No orders found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="px-4 py-3 border-t">{{ $orders->links() }}</div>
</div>
@endsection
