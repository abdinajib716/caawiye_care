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
        Schema::create('lab_test_booking_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lab_test_booking_id');
            $table->unsignedBigInteger('lab_test_id');
            $table->unsignedBigInteger('provider_id');
            $table->string('test_name');
            $table->string('provider_name');
            $table->decimal('cost', 10, 2);
            $table->decimal('commission_percentage', 5, 2);
            $table->enum('commission_type', ['bill_provider', 'bill_customer']);
            $table->decimal('commission_amount', 10, 2);
            $table->decimal('profit', 10, 2);
            $table->decimal('total', 10, 2);
            $table->timestamps();

            // Foreign keys
            $table->foreign('lab_test_booking_id')->references('id')->on('lab_test_bookings')->onDelete('cascade');
            $table->foreign('lab_test_id')->references('id')->on('lab_tests')->onDelete('cascade');
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');

            // Indexes for performance
            $table->index('lab_test_booking_id');
            $table->index('lab_test_id');
            $table->index('provider_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_test_booking_items');
    }
};
