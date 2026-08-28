@extends('admin.layouts.app')
@section('title', 'Change Password')
@section('content')
<div class="max-w-md mx-auto bg-white rounded-2xl shadow p-6">
  <h1 class="text-xl font-bold mb-1">Change Password</h1>
  <p class="text-sm text-gray-500 mb-6">
    @if(auth()->user()->must_change_password)
      You're using a temporary password. Please set a new one to continue.
    @else
      Update your login password.
    @endif
  </p>

  <form method="POST" action="{{ route('admin.password.update') }}" class="space-y-4">
    @csrf @method('PUT')

    @unless(auth()->user()->must_change_password)
    <div>
      <label class="block text-sm font-medium mb-1">Current Password</label>
      <input type="password" name="current_password" required class="w-full px-4 py-2.5 border rounded-lg">
    </div>
    @endunless

    <div>
      <label class="block text-sm font-medium mb-1">New Password</label>
      <input type="password" name="password" required class="w-full px-4 py-2.5 border rounded-lg">
      <p class="text-xs text-gray-400 mt-1">Minimum 8 characters, upper + lower case, at least one number.</p>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Confirm New Password</label>
      <input type="password" name="password_confirmation" required class="w-full px-4 py-2.5 border rounded-lg">
    </div>

    <button class="w-full bg-gray-900 text-white py-2.5 rounded-lg font-semibold hover:bg-gray-800">
      Update Password
    </button>
  </form>
</div>
@endsection
