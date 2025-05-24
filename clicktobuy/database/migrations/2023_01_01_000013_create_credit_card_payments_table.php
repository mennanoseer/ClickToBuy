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
        Schema::create('credit_card_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_id');
            $table->string('card_number', 16);
            $table->string('card_holder', 100);
            $table->string('expiry_date', 5);
            $table->string('cvv', 3);
            $table->timestamps();

            $table->primary('payment_id');
            $table->foreign('payment_id')->references('payment_id')->on('payments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_card_payments');
    }
};
