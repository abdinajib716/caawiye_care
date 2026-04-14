<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_collections', function (Blueprint $table) {
            $table->id();
            $table->string('request_id')->unique();
            
            // Customer Information
            $table->string('customer_name');
            $table->string('customer_phone');
            
            // Patient Information
            $table->string('patient_name');
            $table->string('patient_reference')->nullable();
            
            // Provider Information
            $table->enum('provider_type', ['hospital', 'laboratory', 'supplier', 'other']);
            $table->string('provider_name');
            $table->text('provider_address')->nullable();
            
            // Collection Details
            $table->boolean('delivery_required')->default(false);
            $table->date('delivery_date')->nullable();
            $table->time('delivery_time')->nullable();
            $table->text('internal_notes')->nullable();
            
            // Assignment (Internal Tracking)
            $table->foreignId('assigned_staff_id')->constrained('users')->onDelete('restrict');
            $table->text('assignment_notes')->nullable();
            
            // Payment
            $table->enum('payment_method', ['evc_plus', 'e_dahab']);
            $table->string('payment_reference')->nullable();
            $table->enum('payment_status', ['pending', 'verified', 'failed'])->default('pending');
            $table->timestamp('payment_verified_at')->nullable();
            
            // Charges
            $table->decimal('service_charge', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            
            // Status
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('status');
            $table->index('payment_status');
            $table->index('assigned_staff_id');
            $table->index('created_at');
        });

        // Audit log table for status changes
        Schema::create('report_collection_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_collection_id')->constrained()->onDelete('cascade');
            $table->string('action');
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index('report_collection_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_collection_logs');
        Schema::dropIfExists('report_collections');
    }
};
