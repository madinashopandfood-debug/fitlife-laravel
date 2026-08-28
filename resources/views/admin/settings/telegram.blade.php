@extends('admin.layouts.app')
@section('title', 'Telegram Settings')
@section('content')

<h1 class="text-2xl font-bold mb-2">Telegram Settings</h1>
<p class="text-sm mb-6">
  Status:
  @if($tokenConfigured && $chatId)
    <span class="text-green-700 font-medium">● Connected</span>
  @else
    <span class="text-red-600 font-medium">● Not Connected</span>
  @endif
</p>

<form method="POST" action="{{ route('admin.settings.telegram.update') }}" class="bg-white rounded-2xl shadow p-6 max-w-xl space-y-4">
  @csrf @method('PUT')

  <div>
    <label class="block text-sm font-medium mb-1">Telegram Bot Token</label>
    <input type="password" name="telegram_bot_token" placeholder="{{ $tokenConfigured ? '•••••••• (already set — leave blank to keep)' : 'Paste bot token from @BotFather' }}"
           class="w-full px-4 py-2.5 border rounded-lg">
    <p class="text-xs text-gray-400 mt-1">Stored encrypted on the server. Never shown in the browser or exposed to the customer frontend.</p>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Telegram Group Chat ID</label>
    <input type="text" name="telegram_chat_id" value="{{ old('telegram_chat_id', $chatId) }}" placeholder="-1001234567890"
           class="w-full px-4 py-2.5 border rounded-lg">
  </div>
  <label class="flex items-center gap-2 text-sm">
    <input type="checkbox" name="telegram_enabled" value="1" @checked($enabled)>
    Enable Telegram Notifications
  </label>

  <div class="flex gap-3 pt-2">
    <button class="bg-gray-900 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-gray-800">Save Settings</button>
  </div>
</form>

<form method="POST" action="{{ route('admin.settings.telegram.test') }}" class="mt-4">
  @csrf
  <button class="px-5 py-2.5 rounded-lg border font-medium hover:bg-gray-50">Test Telegram Connection</button>
</form>
@endsection
