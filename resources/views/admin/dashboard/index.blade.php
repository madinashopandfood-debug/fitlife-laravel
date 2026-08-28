@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('content')

<h1 class="text-2xl font-bold mb-6">Dashboard</h1>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
  @php
    $cards = [
      ['label' => 'Total Orders', 'value' => $stats['total'], 'color' => 'bg-gray-900'],
      ['label' => 'Pending', 'value' => $stats['pending'], 'color' => 'bg-yellow-500'],
      ['label' => 'Confirmed', 'value' => $stats['confirmed'], 'color' => 'bg-blue-500'],
      ['label' => 'Hold', 'value' => $stats['hold'], 'color' => 'bg-orange-500'],
      ['label' => 'Cancelled', 'value' => $stats['cancelled'], 'color' => 'bg-red-500'],
      ['label' => 'Delivered', 'value' => $stats['delivered'], 'color' => 'bg-green-600'],
    ];
  @endphp
  @foreach($cards as $card)
    <div class="rounded-2xl shadow p-5 text-white {{ $card['color'] }}">
      <div class="text-3xl font-bold">{{ $card['value'] }}</div>
      <div class="text-sm opacity-90 mt-1">{{ $card['label'] }}</div>
    </div>
  @endforeach
</div>

<div class="bg-white rounded-2xl shadow p-5 mb-8">
  <div class="text-sm text-gray-500">Today's Orders</div>
  <div class="text-3xl font-bold">{{ $stats['today'] }}</div>
</div>

<div class="bg-white rounded-2xl shadow overflow-hidden">
  <div class="px-5 py-4 border-b flex items-center justify-between">
    <h2 class="font-semibold">Recent Orders</h2>
    <a href="{{ route('admin.orders.index') }}" class="text-sm text-blue-600 hover:underline">View all →</a>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 text-gray-500 text-left">
        <tr>
          <th class="px-5 py-3">Order ID</th>
          <th class="px-5 py-3">Customer</th>
          <th class="px-5 py-3">Phone</th>
          <th class="px-5 py-3">Status</th>
          <th class="px-5 py-3">Time</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($recentOrders as $order)
          <tr class="hover:bg-gray-50">
            <td class="px-5 py-3"><a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 font-medium">{{ $order->order_code }}</a></td>
            <td class="px-5 py-3">{{ $order->customer_name }}</td>
            <td class="px-5 py-3">{{ $order->phone }}</td>
            <td class="px-5 py-3"><span class="px-2 py-1 rounded-full text-xs font-medium {{ $order->statusBadgeClass() }}">{{ $order->status }}</span></td>
            <td class="px-5 py-3 text-gray-500">{{ $order->created_at->diffForHumans() }}</td>
          </tr>
        @empty
          <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">No orders found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
