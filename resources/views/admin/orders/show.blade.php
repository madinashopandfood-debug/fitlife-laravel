@extends('admin.layouts.app')
@section('title', 'Order ' . $order->order_code)
@section('content')

<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl font-bold">Order {{ $order->order_code }}</h1>
  <span class="px-3 py-1 rounded-full text-sm font-medium {{ $order->statusBadgeClass() }}">{{ $order->status }}</span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="lg:col-span-2 bg-white rounded-2xl shadow p-6 space-y-4">
    <div>
      <div class="text-xs text-gray-400 uppercase">Customer Name</div>
      <div class="font-medium">{{ $order->customer_name }}</div>
    </div>
    <div class="flex items-start justify-between">
      <div>
        <div class="text-xs text-gray-400 uppercase">Mobile Number</div>
        <div class="font-medium">{{ $order->phone }}</div>
      </div>
      <div class="flex gap-2 text-sm">
        <a href="tel:{{ $order->phone }}" class="px-3 py-1.5 rounded-lg bg-gray-900 text-white">📞 Call</a>
        <button onclick="navigator.clipboard.writeText('{{ $order->phone }}')" class="px-3 py-1.5 rounded-lg border">Copy</button>
      </div>
    </div>
    <div class="flex items-start justify-between">
      <div>
        <div class="text-xs text-gray-400 uppercase">Address</div>
        <div class="font-medium">{{ $order->address }}</div>
      </div>
      <button onclick="navigator.clipboard.writeText(`{{ $order->address }}`)" class="px-3 py-1.5 rounded-lg border text-sm">Copy</button>
    </div>
    <div>
      <div class="text-xs text-gray-400 uppercase">Quantity</div>
      <div class="font-medium">{{ $order->quantity }}</div>
    </div>
    <div>
      <div class="text-xs text-gray-400 uppercase">Customer Note</div>
      <div class="font-medium">{{ $order->note ?: '—' }}</div>
    </div>
    <div class="pt-2 flex gap-2">
      <button onclick="navigator.clipboard.writeText(`Name: {{ $order->customer_name }}\nPhone: {{ $order->phone }}\nAddress: {{ $order->address }}\nQty: {{ $order->quantity }}\nNote: {{ $order->note }}`)"
              class="px-3 py-2 rounded-lg border text-sm">📋 Copy All Order Info</button>
      <a href="{{ route('admin.orders.edit', $order) }}" class="px-3 py-2 rounded-lg bg-gray-900 text-white text-sm">✏ Edit Order</a>
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow p-6">
    <h2 class="font-semibold mb-4">Meta / Telegram</h2>
    <ul class="text-sm space-y-2">
      <li>Telegram Notified: <strong>{{ $order->telegram_notified ? 'Yes' : 'No' }}</strong></li>
      <li>Pixel Fired (client): <strong>{{ $order->pixel_fired ? 'Yes' : 'No' }}</strong></li>
      <li>CAPI Fired (server): <strong>{{ $order->capi_fired ? 'Yes' : 'No' }}</strong></li>
      <li class="break-all">Event ID: <code class="text-xs">{{ $order->event_id }}</code></li>
    </ul>

    <h2 class="font-semibold mt-6 mb-3">Activity</h2>
    <ul class="text-xs text-gray-500 space-y-2 max-h-64 overflow-y-auto">
      @forelse($logs as $log)
        <li class="border-b pb-2">
          <div>{{ $log->description ?? $log->action }}</div>
          <div class="text-gray-400">{{ $log->user?->name ?? 'System' }} · {{ $log->created_at->diffForHumans() }}</div>
        </li>
      @empty
        <li>No activity recorded yet.</li>
      @endforelse
    </ul>
  </div>
</div>
@endsection
