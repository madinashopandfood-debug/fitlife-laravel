<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    public function isEnabled(): bool
    {
        return (bool) Setting::get('telegram_enabled', false)
            && Setting::getSecret('telegram_bot_token')
            && Setting::get('telegram_chat_id');
    }

    /**
     * Sends the "new order" message to the configured Telegram group.
     * IMPORTANT: never throws — a Telegram failure must never block or
     * roll back the customer's order. Errors are logged and the order's
     * telegram_notified flag stays false so Admin can see it in the panel.
     */
    public function notifyNewOrder(Order $order): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        $text = "🛍️ NEW FIT LIFE ORDER\n\n"
            . "📌 Name: {$order->customer_name}\n"
            . "📞 Mobile: {$order->phone}\n"
            . "📍 Address: {$order->address}\n"
            . "📦 Quantity: {$order->quantity}\n"
            . "💬 Note: " . ($order->note ?: '-') . "\n"
            . "🕐 Time: {$order->created_at->timezone(config('app.timezone'))->format('d M Y, h:i A')}\n"
            . "🆔 Order ID: {$order->order_code}";

        return $this->send($text);
    }

    public function send(string $text): bool
    {
        $token = Setting::getSecret('telegram_bot_token');
        $chatId = Setting::get('telegram_chat_id');

        if (! $token || ! $chatId) {
            return false;
        }

        try {
            $response = Http::timeout(10)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
            ]);

            if (! $response->successful()) {
                Log::warning('Telegram send failed', ['body' => $response->body()]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Telegram send exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Used by the "Test Telegram Connection" button in Admin Settings.
     */
    public function testConnection(): array
    {
        $token = Setting::getSecret('telegram_bot_token');
        $chatId = Setting::get('telegram_chat_id');

        if (! $token || ! $chatId) {
            return ['connected' => false, 'message' => 'Bot token or chat ID missing.'];
        }

        try {
            $me = Http::timeout(10)->get("https://api.telegram.org/bot{$token}/getMe");
            if (! $me->successful()) {
                return ['connected' => false, 'message' => 'Invalid bot token.'];
            }

            $sent = $this->send('✅ Fit Life Admin Panel: Telegram connection test successful.');

            return $sent
                ? ['connected' => true, 'message' => 'Connected. Test message sent to the group.']
                : ['connected' => false, 'message' => 'Bot token is valid but the group message failed. Check the Chat ID.'];
        } catch (\Throwable $e) {
            return ['connected' => false, 'message' => $e->getMessage()];
        }
    }
}
