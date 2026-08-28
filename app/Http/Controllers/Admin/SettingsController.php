<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use App\Services\MetaCapiService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * All actions here are Admin-only (see routes/web.php: role:admin).
 * Secrets (Telegram bot token, Meta CAPI access token) are encrypted at
 * rest via Setting::setSecret() and are never echoed back into the view —
 * the form only shows whether a secret "is configured".
 */
class SettingsController extends Controller
{
    public function general()
    {
        return view('admin.settings.general', [
            'settings' => Setting::many(['site_name', 'currency', 'timezone']),
        ]);
    }

    public function updateGeneral(Request $request)
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:120'],
            'currency' => ['required', 'string', 'max:10'],
            'timezone' => ['required', 'string', 'max:60'],
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        ActivityLog::record(Auth::id(), 'settings.updated', null, 'General settings updated');

        return back()->with('success', 'General settings saved.');
    }

    public function telegram()
    {
        return view('admin.settings.telegram', [
            'chatId' => Setting::get('telegram_chat_id'),
            'enabled' => (bool) Setting::get('telegram_enabled', false),
            'tokenConfigured' => (bool) Setting::getSecret('telegram_bot_token'),
        ]);
    }

    public function updateTelegram(Request $request)
    {
        $data = $request->validate([
            'telegram_bot_token' => ['nullable', 'string'],
            'telegram_chat_id' => ['nullable', 'string', 'max:60'],
            'telegram_enabled' => ['nullable', 'boolean'],
        ]);

        if (! empty($data['telegram_bot_token'])) {
            Setting::setSecret('telegram_bot_token', $data['telegram_bot_token']);
        }
        Setting::set('telegram_chat_id', $data['telegram_chat_id'] ?? null);
        Setting::set('telegram_enabled', $request->boolean('telegram_enabled'));

        ActivityLog::record(Auth::id(), 'settings.telegram_updated', null, 'Telegram settings updated');

        return back()->with('success', 'Telegram settings saved.');
    }

    public function testTelegram(TelegramService $telegram)
    {
        $result = $telegram->testConnection();
        return back()->with($result['connected'] ? 'success' : 'error', $result['message']);
    }

    public function metaPixel()
    {
        return view('admin.settings.meta-pixel', [
            'pixelId' => Setting::get('meta_pixel_id'),
            'enabled' => (bool) Setting::get('pixel_enabled', false),
            'purchaseEnabled' => (bool) Setting::get('purchase_event_enabled', true),
        ]);
    }

    public function updateMetaPixel(Request $request)
    {
        $data = $request->validate([
            'meta_pixel_id' => ['nullable', 'string', 'max:60'],
            'pixel_enabled' => ['nullable', 'boolean'],
            'purchase_event_enabled' => ['nullable', 'boolean'],
        ]);

        Setting::set('meta_pixel_id', $data['meta_pixel_id'] ?? null);
        Setting::set('pixel_enabled', $request->boolean('pixel_enabled'));
        Setting::set('purchase_event_enabled', $request->boolean('purchase_event_enabled'));

        ActivityLog::record(Auth::id(), 'settings.pixel_updated', null, 'Meta Pixel settings updated');

        return back()->with('success', 'Meta Pixel settings saved.');
    }

    public function metaCapi()
    {
        return view('admin.settings.meta-capi', [
            'pixelId' => Setting::get('capi_pixel_id'),
            'testEventCode' => Setting::get('capi_test_event_code'),
            'enabled' => (bool) Setting::get('capi_enabled', false),
            'tokenConfigured' => (bool) Setting::getSecret('capi_access_token'),
        ]);
    }

    public function updateMetaCapi(Request $request)
    {
        $data = $request->validate([
            'capi_pixel_id' => ['nullable', 'string', 'max:60'],
            'capi_access_token' => ['nullable', 'string'],
            'capi_test_event_code' => ['nullable', 'string', 'max:60'],
            'capi_enabled' => ['nullable', 'boolean'],
        ]);

        Setting::set('capi_pixel_id', $data['capi_pixel_id'] ?? null);
        if (! empty($data['capi_access_token'])) {
            Setting::setSecret('capi_access_token', $data['capi_access_token']);
        }
        Setting::set('capi_test_event_code', $data['capi_test_event_code'] ?? null);
        Setting::set('capi_enabled', $request->boolean('capi_enabled'));

        ActivityLog::record(Auth::id(), 'settings.capi_updated', null, 'Meta CAPI settings updated');

        return back()->with('success', 'Meta CAPI settings saved.');
    }

    public function testCapi(MetaCapiService $capi)
    {
        $result = $capi->testConnection();
        return back()->with($result['connected'] ? 'success' : 'error', $result['message']);
    }
}
