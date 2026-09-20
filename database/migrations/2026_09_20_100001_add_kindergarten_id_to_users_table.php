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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('kindergarten_id')->nullable()->constrained('kindergartens')->nullOnDelete();
            $table->string('role')->default('parent');
            
            $table->index('kindergarten_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['kindergarten_id']);
            $table->dropIndex(['kindergarten_id']);
            $table->dropColumn(['kindergarten_id', 'role']);
        });
    }
};
