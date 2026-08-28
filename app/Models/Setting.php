<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

/**
 * Simple key/value settings store.
 *
 * Secret values (Telegram bot token, Meta CAPI access token) are encrypted
 * at rest using Laravel's encrypter (APP_KEY) via the *Secret helpers below.
 * They are NEVER returned to the browser — only "is configured" booleans are.
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("setting:{$key}", function () use ($key, $default) {
            $row = self::where('key', $key)->first();
            return $row ? $row->value : $default;
        });
    }

    public static function set(string $key, $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting:{$key}");
    }

    public static function getSecret(string $key): ?string
    {
        $encrypted = self::get($key);
        if (! $encrypted) {
            return null;
        }

        try {
            return Crypt::decryptString($encrypted);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function setSecret(string $key, ?string $plain): void
    {
        if ($plain === null || $plain === '') {
            self::set($key, null);
            return;
        }

        self::set($key, Crypt::encryptString($plain));
    }

    public static function many(array $keys): array
    {
        $out = [];
        foreach ($keys as $key) {
            $out[$key] = self::get($key);
        }
        return $out;
    }
}
