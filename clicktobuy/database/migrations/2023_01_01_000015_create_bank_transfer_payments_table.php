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
        Schema::create('bank_transfer_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_id');
            $table->string('bank_name', 100);
            $table->string('account_number', 50);
            $table->string('routing_number', 50);
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
        Schema::dropIfExists('bank_transfer_payments');
    }
};
