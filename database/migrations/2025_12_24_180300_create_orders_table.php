<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique()->nullable();
            $table->string('customer_name');
            $table->string('customer_mobile');
            $table->text('customer_address');
            $table->enum('delivery_location', ['dhaka', 'outside-dhaka']);
            $table->text('order_notes')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_charge', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->enum('order_status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->string('payment_status')->default('unpaid');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};