<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Admin') · {{ config('app.name') }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
  body{font-family: ui-sans-serif, system-ui, -apple-system, sans-serif;}
  .nav-link.active{background:#111827;color:#fff;}
</style>
</head>
<body class="bg-gray-100 text-gray-800">
<div class="flex min-h-screen">

  <!-- Sidebar -->
  <aside class="w-64 bg-gray-900 text-gray-200 flex-shrink-0 hidden md:flex md:flex-col">
    <div class="px-5 py-5 text-xl font-bold text-white border-b border-gray-800">Fit Life Admin</div>
    <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
      <a href="{{ route('admin.dashboard') }}" class="nav-link flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">📊 Dashboard</a>
      <a href="{{ route('admin.orders.index') }}" class="nav-link flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">📦 Orders</a>
      @if(auth()->user()?->isAdmin())
      <div class="pt-4 pb-1 px-3 text-xs uppercase tracking-wider text-gray-500">Admin</div>
      <a href="{{ route('admin.moderators.index') }}" class="nav-link flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.moderators.*') ? 'active' : '' }}">👥 Moderators</a>
      <a href="{{ route('admin.settings.general') }}" class="nav-link flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.settings.general') ? 'active' : '' }}">⚙️ General Settings</a>
      <a href="{{ route('admin.settings.telegram') }}" class="nav-link flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.settings.telegram') ? 'active' : '' }}">💬 Telegram</a>
      <a href="{{ route('admin.settings.pixel') }}" class="nav-link flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.settings.pixel') ? 'active' : '' }}">📈 Meta Pixel</a>
      <a href="{{ route('admin.settings.capi') }}" class="nav-link flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.settings.capi') ? 'active' : '' }}">🔌 Meta CAPI</a>
      @endif
    </nav>
    <div class="px-3 py-4 border-t border-gray-800 text-sm">
      <div class="px-3 pb-2 text-gray-400 truncate">{{ auth()->user()?->email }}</div>
      <a href="{{ route('admin.password.edit') }}" class="block px-3 py-2 rounded-lg hover:bg-gray-800">🔑 Change Password</a>
      <form method="POST" action="{{ route('admin.logout') }}">@csrf
        <button class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-800">🚪 Logout</button>
      </form>
    </div>
  </aside>

  <!-- Main -->
  <div class="flex-1 flex flex-col min-w-0">
    <header class="bg-white border-b px-4 py-3 flex items-center justify-between md:hidden">
      <span class="font-bold">Fit Life Admin</span>
      <form method="POST" action="{{ route('admin.logout') }}">@csrf
        <button class="text-sm text-red-600">Logout</button>
      </form>
    </header>

    <main class="flex-1 p-4 md:p-8">
      @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-3 text-sm">{{ session('error') }}</div>
      @endif
      @if(session('warning'))
        <div class="mb-4 rounded-lg bg-yellow-100 text-yellow-800 px-4 py-3 text-sm">{{ session('warning') }}</div>
      @endif
      @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-3 text-sm">
          <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
          </ul>
        </div>
      @endif

      @yield('content')
    </main>
  </div>
</div>
</body>
</html>
