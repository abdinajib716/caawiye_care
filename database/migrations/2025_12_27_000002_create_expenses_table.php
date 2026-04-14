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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number')->unique();
            $table->date('expense_date');
            $table->unsignedBigInteger('category_id');
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2);
            $table->enum('transaction_method', ['cash', 'evc', 'edahab', 'bank'])->default('cash');
            $table->string('paid_to')->nullable();
            $table->string('payee_type')->nullable();
            $table->unsignedBigInteger('payee_id')->nullable();
            $table->unsignedBigInteger('related_order_id')->nullable();
            $table->string('related_order_type')->nullable();
            $table->string('attachment_path')->nullable();
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'paid', 'rejected'])->default('draft');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('expense_categories')->onDelete('restrict');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->index('expense_number');
            $table->index('expense_date');
            $table->index('status');
            $table->index('category_id');
            $table->index(['payee_type', 'payee_id']);
            $table->index(['related_order_type', 'related_order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
