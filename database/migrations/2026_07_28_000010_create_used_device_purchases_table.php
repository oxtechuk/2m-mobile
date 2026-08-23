<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('used_device_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('device_name', 150);
            $table->string('imei', 100);
            $table->decimal('purchase_price', 12, 2);
            $table->decimal('estimated_repair_cost', 12, 2)->default(0);
            $table->enum('status', ['purchased', 'in_maintenance', 'offered_for_sale', 'sold'])->default('purchased');
            $table->string('national_id_photo', 255)->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('imei');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('used_device_purchases');
    }
};
