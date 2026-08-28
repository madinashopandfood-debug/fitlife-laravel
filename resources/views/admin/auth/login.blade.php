<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login · {{ config('app.name') }}</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center p-4">
  <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl p-8">
    <h1 class="text-2xl font-bold text-center mb-1">Fit Life Admin</h1>
    <p class="text-center text-gray-500 text-sm mb-6">Sign in to manage orders</p>

    @if($errors->any())
      <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-3 text-sm">
        @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
      @csrf
      <div>
        <label class="block text-sm font-medium mb-1">Email</label>
        <input type="email" name="email" required value="{{ old('email') }}"
               class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-gray-900 outline-none">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Password</label>
        <input type="password" name="password" required
               class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-gray-900 outline-none">
      </div>
      <label class="flex items-center gap-2 text-sm text-gray-600">
        <input type="checkbox" name="remember"> Remember me
      </label>
      <button class="w-full bg-gray-900 text-white py-2.5 rounded-lg font-semibold hover:bg-gray-800">
        Sign In
      </button>
    </form>
  </div>
</body>
</html>
