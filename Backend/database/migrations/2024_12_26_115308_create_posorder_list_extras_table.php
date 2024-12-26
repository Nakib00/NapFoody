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
        Schema::create('posorder_list_extras', function (Blueprint $table) {
            $table->id();
            $table->integer('posorder_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->integer('extra_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posorder_list_extras');
    }
};
