<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        static $sequence = 1;

        $orderCode = 'FL-'.str_pad((string) $sequence++, 5, '0', STR_PAD_LEFT);

        return [
            'order_code' => $orderCode,
            'customer_name' => fake()->name(),
            'phone' => fake()->numerify('01#########'),
            'address' => fake()->address(),
            'quantity' => (string) fake()->numberBetween(1, 3),
            'note' => fake()->optional()->sentence(),
            'status' => Order::STATUS_PENDING,
            'event_id' => 'order_'.$orderCode.'_'.Str::random(10),
            'pixel_fired' => false,
            'capi_fired' => false,
            'telegram_notified' => false,
            'order_time' => now(),
        ];
    }
}
