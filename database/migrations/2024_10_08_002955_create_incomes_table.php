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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->decimal('value', 10, 2);
            $table->string('category_id');
            $table->string('payment_method_id');
            $table->text('notes')->nullable();
            $table->dateTime('datetime');
            $table->string('receipt_url')->nullable(); // URL for receipt
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
