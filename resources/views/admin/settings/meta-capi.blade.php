@extends('admin.layouts.app')
@section('title', 'Meta CAPI Settings')
@section('content')

<h1 class="text-2xl font-bold mb-2">Meta Conversions API</h1>
<p class="text-sm mb-6">
  Status:
  @if($tokenConfigured && $pixelId)
    <span class="text-green-700 font-medium">● Configured</span>
  @else
    <span class="text-red-600 font-medium">● Not Configured</span>
  @endif
</p>

<form method="POST" action="{{ route('admin.settings.capi.update') }}" class="bg-white rounded-2xl shadow p-6 max-w-xl space-y-4">
  @csrf @method('PUT')

  <div>
    <label class="block text-sm font-medium mb-1">Dataset / Pixel ID</label>
    <input type="text" name="capi_pixel_id" value="{{ old('capi_pixel_id', $pixelId) }}" class="w-full px-4 py-2.5 border rounded-lg">
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Access Token</label>
    <input type="password" name="capi_access_token" placeholder="{{ $tokenConfigured ? '•••••••• (already set — leave blank to keep)' : 'Paste the CAPI access token' }}"
           class="w-full px-4 py-2.5 border rounded-lg">
    <p class="text-xs text-gray-400 mt-1">Stored encrypted on the server. Never appears in index.html, browser JavaScript, or any public API response.</p>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Test Event Code <span class="text-gray-400 font-normal">(optional)</span></label>
    <input type="text" name="capi_test_event_code" value="{{ old('capi_test_event_code', $testEventCode) }}" placeholder="TEST12345"
           class="w-full px-4 py-2.5 border rounded-lg">
  </div>
  <label class="flex items-center gap-2 text-sm">
    <input type="checkbox" name="capi_enabled" value="1" @checked($enabled)>
    Enable CAPI
  </label>

  <button class="bg-gray-900 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-gray-800">Save Settings</button>
</form>

<form method="POST" action="{{ route('admin.settings.capi.test') }}" class="mt-4">
  @csrf
  <button class="px-5 py-2.5 rounded-lg border font-medium hover:bg-gray-50">Test CAPI</button>
</form>
@endsection
