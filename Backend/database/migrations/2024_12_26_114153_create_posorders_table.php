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
        Schema::create('posorders', function (Blueprint $table) {
            $table->id();
            $table->string('orders_id');
            $table->integer('admin_id');
            $table->integer('stuff_id')->nullable();
            $table->integer('customer_id');
            $table->integer('branch_id');
            $table->string('total_price');
            $table->string('payment_method');
            $table->string('discount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posorders');
    }
};
