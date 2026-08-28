<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaCapiService
{
    public function isEnabled(): bool
    {
        return (bool) Setting::get('capi_enabled', false)
            && Setting::get('capi_pixel_id')
            && Setting::getSecret('capi_access_token');
    }

    /**
     * Sends a server-side Purchase event to Meta's Conversions API.
     *
     * event_id MUST match the browser Pixel's event_id for the same order
     * (see resources/views + the /api/orders response) so Meta can
     * deduplicate the browser + server Purchase events.
     *
     * Never throws: CAPI failures must not affect the customer's order.
     */
    public function sendPurchase(Order $order, string $eventId, float $value, string $currency = 'BDT'): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        $pixelId = Setting::get('capi_pixel_id');
        $accessToken = Setting::getSecret('capi_access_token');
        $testCode = Setting::get('capi_test_event_code');

        $payload = [
            'data' => [[
                'event_name' => 'Purchase',
                'event_time' => now()->timestamp,
                'event_id' => $eventId,
                'action_source' => 'website',
                'user_data' => [
                    // Meta requires hashed PII. Phone is the only identifier we collect.
                    'ph' => [hash('sha256', preg_replace('/\D/', '', $order->phone))],
                ],
                'custom_data' => [
                    'currency' => $currency,
                    'value' => $value,
                    'contents' => [[
                        'id' => 'fitlife-supplement',
                        'quantity' => (int) preg_replace('/\D/', '', $order->quantity) ?: 1,
                    ]],
                ],
            ]],
        ];

        if ($testCode) {
            $payload['test_event_code'] = $testCode;
        }

        try {
            $response = Http::timeout(10)
                ->post("https://graph.facebook.com/v19.0/{$pixelId}/events", array_merge($payload, [
                    'access_token' => $accessToken,
                ]));

            if (! $response->successful()) {
                Log::warning('Meta CAPI send failed', ['body' => $response->body()]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Meta CAPI exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Used by the "Test CAPI" button in Admin Settings. Sends a harmless
     * PageView test event using the configured test_event_code.
     */
    public function testConnection(): array
    {
        $pixelId = Setting::get('capi_pixel_id');
        $accessToken = Setting::getSecret('capi_access_token');
        $testCode = Setting::get('capi_test_event_code');

        if (! $pixelId || ! $accessToken) {
            return ['connected' => false, 'message' => 'Dataset/Pixel ID or Access Token missing.'];
        }

        $payload = [
            'data' => [[
                'event_name' => 'PageView',
                'event_time' => now()->timestamp,
                'action_source' => 'website',
                'user_data' => ['client_ip_address' => request()->ip(), 'client_user_agent' => request()->userAgent()],
            ]],
            'access_token' => $accessToken,
        ];

        if ($testCode) {
            $payload['test_event_code'] = $testCode;
        }

        try {
            $response = Http::timeout(10)->post("https://graph.facebook.com/v19.0/{$pixelId}/events", $payload);

            if (! $response->successful()) {
                return ['connected' => false, 'message' => $response->json('error.message', 'Request failed.')];
            }

            return ['connected' => true, 'message' => 'Test event accepted by Meta.'];
        } catch (\Throwable $e) {
            return ['connected' => false, 'message' => $e->getMessage()];
        }
    }
}
