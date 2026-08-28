<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_CONFIRMED = 'CONFIRMED';
    public const STATUS_HOLD = 'HOLD';
    public const STATUS_CANCELLED = 'CANCELLED';
    public const STATUS_DELIVERED = 'DELIVERED';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_HOLD,
        self::STATUS_CANCELLED,
        self::STATUS_DELIVERED,
    ];

    protected $fillable = [
        'order_code',
        'customer_name',
        'phone',
        'address',
        'quantity',
        'note',
        'status',
        'event_id',
        'pixel_fired',
        'capi_fired',
        'telegram_notified',
        'order_time',
    ];

    protected $casts = [
        'pixel_fired' => 'boolean',
        'capi_fired' => 'boolean',
        'telegram_notified' => 'boolean',
        'order_time' => 'datetime',
    ];

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('order_code', 'like', "%{$term}%")
                ->orWhere('customer_name', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
                ->orWhere('address', 'like', "%{$term}%");
        });
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if (! $status) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function scopeDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_CONFIRMED => 'bg-blue-100 text-blue-800',
            self::STATUS_HOLD => 'bg-orange-100 text-orange-800',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800',
            self::STATUS_DELIVERED => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
