<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kindergarten_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['recurring', 'one_time'])->default('recurring');
            $table->decimal('amount', 10, 2);
            $table->enum('frequency', ['monthly', 'quarterly', 'yearly', 'once'])->default('monthly');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['kindergarten_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_templates');
    }
};
