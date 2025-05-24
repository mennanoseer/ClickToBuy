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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id('shipment_id');
            $table->dateTime('shipment_date')->nullable();
            $table->string('address', 100);
            $table->string('city', 100);
            $table->string('state', 20);
            $table->string('country', 50);
            $table->string('zip_code', 10);
            $table->string('tracking_number', 100)->nullable();
            $table->string('carrier', 50)->nullable();
            $table->string('status', 50)->default('processing');
            $table->unsignedBigInteger('order_id');
            $table->timestamps();

            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
