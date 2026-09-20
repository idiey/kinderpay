<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kindergarten_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('method', ['fpx', 'card', 'ewallet', 'cash', 'bank_transfer', 'cheque'])->default('fpx');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('gateway_ref')->nullable();
            $table->json('gateway_response')->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->string('proof_path', 500)->nullable();
            $table->string('receipt_number', 50)->nullable()->unique();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('gateway_ref');
            $table->index(['invoice_id', 'status']);
            $table->index(['kindergarten_id', 'paid_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
