<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique(); // human-friendly order id, e.g. FL-0001
            $table->string('customer_name');
            $table->string('phone', 20);
            $table->text('address');
            $table->string('quantity');
            $table->text('note')->nullable();
            $table->enum('status', ['PENDING', 'CONFIRMED', 'HOLD', 'CANCELLED', 'DELIVERED'])
                ->default('PENDING');
            $table->string('event_id')->nullable(); // shared between Pixel + CAPI for dedup
            $table->boolean('pixel_fired')->default(false);
            $table->boolean('capi_fired')->default(false);
            $table->boolean('telegram_notified')->default(false);
            $table->timestamp('order_time')->nullable();
            $table->timestamps();

            $table->index('phone');
            $table->index('customer_name');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
