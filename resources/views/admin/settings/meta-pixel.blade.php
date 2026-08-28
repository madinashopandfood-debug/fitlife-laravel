@extends('admin.layouts.app')
@section('title', 'Meta Pixel Settings')
@section('content')

<h1 class="text-2xl font-bold mb-6">Meta Tracking (Pixel)</h1>

<form method="POST" action="{{ route('admin.settings.pixel.update') }}" class="bg-white rounded-2xl shadow p-6 max-w-xl space-y-4">
  @csrf @method('PUT')

  <div>
    <label class="block text-sm font-medium mb-1">Meta Pixel / Dataset ID</label>
    <input type="text" name="meta_pixel_id" value="{{ old('meta_pixel_id', $pixelId) }}" placeholder="123456789012345"
           class="w-full px-4 py-2.5 border rounded-lg">
    <p class="text-xs text-gray-400 mt-1">Loaded by the customer site via /api/pixel-config — never hardcoded into index.html.</p>
  </div>
  <label class="flex items-center gap-2 text-sm">
    <input type="checkbox" name="pixel_enabled" value="1" @checked($enabled)>
    Enable Pixel
  </label>
  <label class="flex items-center gap-2 text-sm">
    <input type="checkbox" name="purchase_event_enabled" value="1" @checked($purchaseEnabled)>
    Purchase Event Enabled
  </label>

  <button class="bg-gray-900 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-gray-800">Save Settings</button>
</form>

<div class="mt-6 bg-white rounded-2xl shadow p-6 max-w-xl text-sm text-gray-600">
  <h2 class="font-semibold text-gray-800 mb-2">Events fired by the customer site</h2>
  <ul class="list-disc list-inside space-y-1">
    <li>PageView — on every page load</li>
    <li>ViewContent — on the product section</li>
    <li>InitiateCheckout — when the order form opens</li>
    <li>Purchase — only after the order is saved successfully, using the same event_id as the server-side CAPI Purchase (see Meta CAPI page) for deduplication</li>
  </ul>
</div>
@endsection
