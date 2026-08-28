@extends('admin.layouts.app')
@section('title', 'Moderators')
@section('content')

<h1 class="text-2xl font-bold mb-6">Manage Moderators & Admins</h1>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="bg-white rounded-2xl shadow p-6">
    <h2 class="font-semibold mb-4">Add Account</h2>
    <form method="POST" action="{{ route('admin.moderators.store') }}" class="space-y-3">
      @csrf
      <div>
        <label class="block text-sm font-medium mb-1">Name</label>
        <input type="text" name="name" required class="w-full px-3 py-2 border rounded-lg text-sm">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Email</label>
        <input type="email" name="email" required class="w-full px-3 py-2 border rounded-lg text-sm">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Role</label>
        <select name="role" class="w-full px-3 py-2 border rounded-lg text-sm">
          <option value="moderator">Moderator</option>
          <option value="admin">Admin</option>
        </select>
      </div>
      <button class="w-full bg-gray-900 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-gray-800">Create Account</button>
      <p class="text-xs text-gray-400">A random temporary password is generated and shown once — the account is forced to change it on first login.</p>
    </form>
  </div>

  <div class="lg:col-span-2 bg-white rounded-2xl shadow overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 text-gray-500 text-left">
        <tr>
          <th class="px-4 py-3">Name</th>
          <th class="px-4 py-3">Email</th>
          <th class="px-4 py-3">Role</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @foreach($users as $user)
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-3">
              <form method="POST" action="{{ route('admin.moderators.update', $user) }}" class="flex items-center gap-2">
                @csrf @method('PUT')
                <input type="text" name="name" value="{{ $user->name }}" class="px-2 py-1 border rounded text-sm w-28">
            </td>
            <td class="px-4 py-3 text-gray-500">{{ $user->email }}</td>
            <td class="px-4 py-3">
                <select name="role" class="px-2 py-1 border rounded text-sm">
                  <option value="moderator" @selected($user->role === 'moderator')>Moderator</option>
                  <option value="admin" @selected($user->role === 'admin')>Admin</option>
                </select>
            </td>
            <td class="px-4 py-3">
                <label class="inline-flex items-center gap-1 text-xs">
                  <input type="checkbox" name="is_active" value="1" @checked($user->is_active)> Active
                </label>
            </td>
            <td class="px-4 py-3">
              <div class="flex justify-end gap-2 items-center">
                <button class="text-blue-600 hover:underline text-xs">Save</button>
              </form>
              <form method="POST" action="{{ route('admin.moderators.reset-password', $user) }}" onsubmit="return confirm('Reset password for {{ $user->email }}?');">
                @csrf
                <button class="text-gray-600 hover:underline text-xs">Reset PW</button>
              </form>
              @if($user->id !== auth()->id())
              <form method="POST" action="{{ route('admin.moderators.destroy', $user) }}" onsubmit="return confirm('Delete {{ $user->email }}?');">
                @csrf @method('DELETE')
                <button class="text-red-600 hover:underline text-xs">Delete</button>
              </form>
              @endif
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
