<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration to add UUID, sync status, and branch tracking columns across all transactional and master tables
     * for Offline-First & Online Synchronization between Local POS and Cloud aaPanel MySQL.
     */
    public function up(): void
    {
        $tables = [
            'sales',
            'sale_items',
            'cash_shifts',
            'products',
            'categories',
            'customers',
            'expenses',
            'wallets',
            'money_transfers',
            'used_device_purchases',
            'maintenance_requests'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'uuid')) {
                        $table->uuid('uuid')->nullable()->after('id')->index();
                    }
                    if (!Schema::hasColumn($tableName, 'sync_status')) {
                        $table->enum('sync_status', ['synced', 'pending', 'conflict', 'failed'])
                              ->default('synced')
                              ->after('created_at')
                              ->index();
                    }
                    if (!Schema::hasColumn($tableName, 'synced_at')) {
                        $table->timestamp('synced_at')->nullable()->after('sync_status');
                    }
                });
            }
        }

        // Create dedicated sync_queue table for outbox offline transactions
        if (!Schema::hasTable('sync_queues')) {
            Schema::create('sync_queues', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->string('entity_type', 100)->index(); // e.g. Sale, CashShift, Expense
                $table->uuid('entity_uuid')->index();
                $table->string('action', 20)->default('create'); // create, update, delete
                $table->json('payload');
                $table->enum('status', ['pending', 'processing', 'synced', 'failed'])->default('pending')->index();
                $table->text('error_log')->nullable();
                $table->integer('attempts')->default(0);
                $table->timestamp('synced_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_queues');

        $tables = [
            'sales',
            'sale_items',
            'cash_shifts',
            'products',
            'categories',
            'customers',
            'expenses',
            'wallets',
            'money_transfers',
            'used_device_purchases',
            'maintenance_requests'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'uuid')) {
                        $table->dropColumn('uuid');
                    }
                    if (Schema::hasColumn($tableName, 'sync_status')) {
                        $table->dropColumn('sync_status');
                    }
                    if (Schema::hasColumn($tableName, 'synced_at')) {
                        $table->dropColumn('synced_at');
                    }
                });
            }
        }
    }
};
