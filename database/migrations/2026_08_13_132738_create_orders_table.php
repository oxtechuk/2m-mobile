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
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->text('shipping_address');
            $table->string('shipping_governorate');
            $table->string('shipping_city')->nullable();
            $table->decimal('shipping_fee', 10, 2)->default(0.00);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_total', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2);
            $table->string('coupon_code')->nullable();
            $table->string('payment_method')->default('cod'); // cod, instapay, wallet
            $table->string('payment_status')->default('pending'); // pending, paid, failed, refunded
            $table->string('payment_id')->nullable(); // external gateway transaction id or txn number
            $table->string('payment_screenshot')->nullable(); // path to the payment proof screenshot
            $table->string('status')->default('pending'); // pending, processing, shipped, delivered, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
