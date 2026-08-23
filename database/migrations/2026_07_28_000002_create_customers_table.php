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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('phone', 20)->unique();
            $table->string('secondary_phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->decimal('total_purchases', 12, 2)->default(0);
            $table->decimal('total_repairs', 12, 2)->default(0);
            $table->integer('loyalty_points')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
