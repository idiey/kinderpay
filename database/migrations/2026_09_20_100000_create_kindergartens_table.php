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
        Schema::create('kindergartens', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('registration_no', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('postcode', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('logo_path', 500)->nullable();
            $table->string('invoice_prefix', 10)->default('INV');
            $table->unsignedTinyInteger('invoice_day')->default(1);
            $table->string('payment_gateway', 50)->default('billplz');
            $table->text('gateway_api_key')->nullable();
            $table->string('gateway_collection_id', 100)->nullable();
            $table->string('timezone', 50)->default('Asia/Kuala_Lumpur');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kindergartens');
    }
};
