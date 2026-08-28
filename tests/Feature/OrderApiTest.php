<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_pixel_config_endpoint_is_reachable(): void
    {
        $response = $this->getJson('/api/pixel-config');

        $response->assertStatus(200);
        $response->assertJsonStructure(['enabled']);
    }

    public function test_order_can_be_submitted_with_valid_data(): void
    {
        $response = $this->postJson('/api/orders', [
            'name' => 'Test Customer',
            'phone' => '01700000000',
            'address' => '123 Test Road, Dhaka',
            'quantity' => '1',
            'note' => 'Please call before delivery',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'order_id', 'event_id']);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', [
            'phone' => '01700000000',
        ]);
    }

    public function test_order_submission_requires_name_phone_and_address(): void
    {
        $response = $this->postJson('/api/orders', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'phone', 'address']);
    }
}
