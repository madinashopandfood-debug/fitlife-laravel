<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'order_id',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public static function record(?int $userId, string $action, ?int $orderId = null, ?string $description = null): void
    {
        self::create([
            'user_id' => $userId,
            'action' => $action,
            'order_id' => $orderId,
            'description' => $description,
        ]);
    }
}
