@extends('admin.layouts.app')
@section('title', 'General Settings')
@section('content')

<h1 class="text-2xl font-bold mb-6">General Settings</h1>

<form method="POST" action="{{ route('admin.settings.general.update') }}" class="bg-white rounded-2xl shadow p-6 max-w-xl space-y-4">
  @csrf @method('PUT')
  <div>
    <label class="block text-sm font-medium mb-1">Website Name</label>
    <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'Fit Life') }}" required class="w-full px-4 py-2.5 border rounded-lg">
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Currency</label>
    <input type="text" name="currency" value="{{ old('currency', $settings['currency'] ?? 'BDT') }}" required class="w-full px-4 py-2.5 border rounded-lg">
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Timezone</label>
    <input type="text" name="timezone" value="{{ old('timezone', $settings['timezone'] ?? 'Asia/Dhaka') }}" required class="w-full px-4 py-2.5 border rounded-lg">
  </div>
  <button class="bg-gray-900 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-gray-800">Save Settings</button>
</form>
@endsection
