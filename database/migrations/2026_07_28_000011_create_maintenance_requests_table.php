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
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 30)->unique();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('technician_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('device_type', 50);
            $table->string('device_model', 100);
            $table->string('device_serial', 100)->nullable();
            $table->text('problem_description');
            $table->text('diagnosis')->nullable();
            $table->json('pre_repair_checklist')->nullable(); // check screen, cameras, etc.
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->decimal('final_cost', 12, 2)->nullable();
            $table->decimal('advance_payment', 12, 2)->default(0);
            $table->enum('status', [
                'received', 'diagnosed', 'waiting_parts', 'in_progress', 'completed', 'delivered', 'cancelled'
            ])->default('received');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->date('estimated_delivery')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('ticket_number');
            $table->index('device_serial');
        });

        Schema::create('maintenance_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_request_id')->constrained('maintenance_requests')->onDelete('cascade');
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50);
            $table->text('notes')->nullable();
            $table->foreignId('changed_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_status_logs');
        Schema::dropIfExists('maintenance_requests');
    }
};
