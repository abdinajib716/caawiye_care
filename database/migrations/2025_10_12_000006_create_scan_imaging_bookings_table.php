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
        Schema::create('scan_imaging_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number')->unique();
            $table->unsignedBigInteger('customer_id');
            $table->string('patient_name');
            $table->unsignedBigInteger('scan_imaging_service_id');
            $table->unsignedBigInteger('provider_id');
            $table->string('service_name');
            $table->string('provider_name');
            $table->decimal('cost', 10, 2);
            $table->decimal('commission_percentage', 5, 2);
            $table->enum('commission_type', ['bill_provider', 'bill_customer']);
            $table->decimal('commission_amount', 10, 2);
            $table->decimal('total', 10, 2);
            $table->dateTime('appointment_time');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->string('provider_payment_status')->default('unpaid');
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('scan_imaging_service_id')->references('id')->on('scan_imaging_services')->onDelete('cascade');
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');

            // Indexes for performance
            $table->index('booking_number');
            $table->index('customer_id');
            $table->index('scan_imaging_service_id');
            $table->index('provider_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('appointment_time');
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scan_imaging_bookings');
    }
};
