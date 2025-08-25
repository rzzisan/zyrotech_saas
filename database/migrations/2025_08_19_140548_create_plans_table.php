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
        Schema::create('plans', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique(); // e.g., Free, Startup, Business
        $table->decimal('price', 8, 2)->default(0.00);
        $table->integer('daily_courier_limit')->default(25);
        $table->integer('monthly_courier_limit')->default(500);
        $table->integer('monthly_incomplete_order_limit')->default(100);
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
