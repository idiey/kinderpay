<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kindergarten_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('employee_id', 50)->nullable();
            $table->string('name');
            $table->text('ic_number')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female'])->default('female');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('position', 100);
            $table->enum('employment_type', ['full_time', 'part_time', 'contract'])->default('full_time');
            $table->date('join_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'resigned', 'terminated'])->default('active');
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->text('bank_name')->nullable();
            $table->text('bank_account')->nullable();
            $table->text('epf_number')->nullable();
            $table->text('socso_number')->nullable();
            $table->text('tax_number')->nullable();
            $table->tinyInteger('epf_category')->default(1);
            $table->string('photo_path', 500)->nullable();
            $table->timestamps();

            $table->index(['kindergarten_id', 'status']);
        });

        Schema::create('allowance_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kindergarten_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->boolean('is_statutory')->default(false);
            $table->timestamps();
        });

        Schema::create('staff_allowances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->foreignId('allowance_type_id')->constrained('allowance_types')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_allowances');
        Schema::dropIfExists('allowance_types');
        Schema::dropIfExists('staff');
    }
};
