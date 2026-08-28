@extends('admin.layouts.app')
@section('title', 'Edit Order ' . $order->order_code)
@section('content')

<h1 class="text-2xl font-bold mb-6">Edit Order {{ $order->order_code }}</h1>

<form method="POST" action="{{ route('admin.orders.update', $order) }}" class="bg-white rounded-2xl shadow p-6 max-w-2xl space-y-4">
  @csrf @method('PUT')

  <div>
    <label class="block text-sm font-medium mb-1">Customer Name</label>
    <input type="text" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" required class="w-full px-4 py-2.5 border rounded-lg">
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Mobile Number</label>
    <input type="text" name="phone" value="{{ old('phone', $order->phone) }}" required class="w-full px-4 py-2.5 border rounded-lg">
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Address</label>
    <textarea name="address" rows="2" required class="w-full px-4 py-2.5 border rounded-lg">{{ old('address', $order->address) }}</textarea>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Quantity</label>
    <input type="text" name="quantity" value="{{ old('quantity', $order->quantity) }}" required class="w-full px-4 py-2.5 border rounded-lg">
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Customer Note</label>
    <textarea name="note" rows="2" class="w-full px-4 py-2.5 border rounded-lg">{{ old('note', $order->note) }}</textarea>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Status</label>
    <select name="status" class="w-full px-4 py-2.5 border rounded-lg">
      @foreach($statuses as $status)
        <option value="{{ $status }}" @selected(old('status', $order->status) === $status)>{{ $status }}</option>
      @endforeach
    </select>
  </div>

  <div class="flex gap-3 pt-2">
    <button class="bg-gray-900 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-gray-800">Save Changes</button>
    <a href="{{ route('admin.orders.show', $order) }}" class="px-5 py-2.5 rounded-lg border">Cancel</a>
  </div>
</form>
@endsection
