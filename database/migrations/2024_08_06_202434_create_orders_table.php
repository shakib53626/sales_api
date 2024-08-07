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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('order_number');
            $table->date('order_date')->nullable();
            $table->string('warehouse')->nullable();
            $table->string('remarks')->nullable();
            $table->string('customer')->nullable();
            $table->integer('carton_bonus')->nullable();
            $table->string('type_1')->nullable();
            $table->string('type_2')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
