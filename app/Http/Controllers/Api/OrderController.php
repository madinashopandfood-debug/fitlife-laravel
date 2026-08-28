<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Setting;
use App\Services\MetaCapiService;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(
        private readonly TelegramService $telegram,
        private readonly MetaCapiService $capi,
    ) {
    }

    /**
     * POST /api/orders
     * Called directly by the existing index.html order form.
     *
     * Flow: validate -> save order -> generate event_id -> Telegram
     * notification -> server-side CAPI Purchase -> return success + the
     * SAME event_id so the browser can fire the matching Pixel Purchase.
     *
     * Telegram/CAPI failures are swallowed on purpose: the customer must
     * always get a successful order confirmation once the row is saved.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:120'],
                'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+ -]{7,20}$/'],
                'address' => ['required', 'string', 'max:500'],
                'quantity' => ['required', 'string', 'max:50'],
                'note' => ['nullable', 'string', 'max:500'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first() ?? 'Invalid data.',
            ], 422);
        }

        $orderCode = $this->generateOrderCode();
        $eventId = "order_{$orderCode}_" . Str::random(10);

        $order = Order::create([
            'order_code' => $orderCode,
            'customer_name' => $data['name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'quantity' => $data['quantity'],
            'note' => $data['note'] ?? null,
            'status' => Order::STATUS_PENDING,
            'event_id' => $eventId,
            'order_time' => now(),
        ]);

        ActivityLog::record(null, 'order.created', $order->id, "New order from {$order->customer_name} ({$order->phone})");

        // --- Telegram notification (never blocks the response) ---
        try {
            $notified = $this->telegram->notifyNewOrder($order);
            if ($notified) {
                $order->update(['telegram_notified' => true]);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        // --- Meta Conversions API Purchase (never blocks the response) ---
        $purchaseValue = (float) Setting::get('purchase_value_default', 0);
        try {
            $sent = $this->capi->sendPurchase($order, $eventId, $purchaseValue, Setting::get('currency', 'BDT'));
            if ($sent) {
                $order->update(['capi_fired' => true]);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'success' => true,
            'order_id' => $order->order_code,
            'event_id' => $eventId,
        ]);
    }

    /**
     * GET /api/pixel-config
     * Public, non-secret config the customer frontend needs to load the
     * Meta Pixel itself and fire the browser Purchase event. The CAPI
     * access token and Telegram bot token are NEVER exposed here.
     */
    public function pixelConfig(): JsonResponse
    {
        return response()->json([
            'enabled' => (bool) Setting::get('pixel_enabled', false),
            'pixel_id' => Setting::get('pixel_enabled', false) ? Setting::get('meta_pixel_id') : null,
            'purchase_event_enabled' => (bool) Setting::get('purchase_event_enabled', true),
            'currency' => Setting::get('currency', 'BDT'),
            'purchase_value_default' => (float) Setting::get('purchase_value_default', 0),
        ]);
    }

    private function generateOrderCode(): string
    {
        $next = (Order::max('id') ?? 0) + 1;
        return 'FL-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}
