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
        // 1. Suppliers Table
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('company_name', 150)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->decimal('opening_balance', 12, 2)->default(0.00);
            $table->decimal('current_balance', 12, 2)->default(0.00); // positive: owed to supplier, negative: credit with supplier
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Purchases Table
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->decimal('paid_amount', 12, 2)->default(0.00);
            $table->decimal('due_amount', 12, 2)->default(0.00);
            $table->enum('payment_status', ['paid', 'partial', 'unpaid'])->default('paid');
            $table->string('payment_method', 50)->default('cash');
            $table->timestamp('purchase_date')->useCurrent();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Purchase Items Table
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_cost', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->json('serials')->nullable();
            $table->timestamps();
        });

        // 4. Supplier Transactions Table
        Schema::create('supplier_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->enum('type', ['purchase_invoice', 'payment_out', 'purchase_return', 'opening_balance']);
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_after', 12, 2)->default(0.00);
            $table->string('payment_method', 50)->nullable();
            $table->string('reference_type', 100)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // 5. Add primary_supplier_id to products table
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('primary_supplier_id')->nullable()->after('category_id')->constrained('suppliers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['primary_supplier_id']);
            $table->dropColumn('primary_supplier_id');
        });

        Schema::dropIfExists('supplier_transactions');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('suppliers');
    }
};
