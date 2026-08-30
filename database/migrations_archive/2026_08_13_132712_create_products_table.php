<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->string('sku')->unique()->nullable();
            $table->string('type')->default('physical'); // physical, digital, course, session
            $table->integer('stock')->default(0);
            $table->string('digital_file_path')->nullable();
            $table->string('digital_file_name')->nullable();
            $table->integer('digital_download_limit')->nullable();
            $table->integer('digital_expiry_days')->nullable();
            $table->string('video_url')->nullable();
            $table->text('benefits')->nullable();
            $table->text('how_to_use')->nullable();
            $table->text('whats_included')->nullable();
            $table->text('suitable_for')->nullable();
            $table->string('badge')->nullable(); // new, bestseller, discount, download
            $table->text('images')->nullable(); // JSON gallery
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pivot: category_product
        Schema::create('category_product', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->primary(['category_id', 'product_id']);
        });

        // Pivot: age_group_product
        Schema::create('age_group_product', function (Blueprint $table) {
            $table->foreignId('age_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->primary(['age_group_id', 'product_id']);
        });

        // Pivot: product_skill
        Schema::create('product_skill', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->primary(['product_id', 'skill_id']);
        });

        // Pivot: need_product
        Schema::create('need_product', function (Blueprint $table) {
            $table->foreignId('need_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->primary(['need_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('need_product');
        Schema::dropIfExists('product_skill');
        Schema::dropIfExists('age_group_product');
        Schema::dropIfExists('category_product');
        Schema::dropIfExists('products');
    }
};
