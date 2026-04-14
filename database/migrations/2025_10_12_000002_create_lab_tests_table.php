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
        Schema::create('lab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('provider_id');
            $table->decimal('cost', 10, 2);
            $table->decimal('commission_percentage', 5, 2)->default(0);
            $table->enum('commission_type', ['bill_provider', 'bill_customer'])->default('bill_provider');
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('profit', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');

            // Indexes for performance
            $table->index('provider_id');
            $table->index('status');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_tests');
    }
};
