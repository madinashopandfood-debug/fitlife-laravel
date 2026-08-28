<?php

namespace Tests\Unit;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_status_badge_class_returns_a_string(): void
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
        ]);

        $this->assertIsString($order->statusBadgeClass());
    }
}
