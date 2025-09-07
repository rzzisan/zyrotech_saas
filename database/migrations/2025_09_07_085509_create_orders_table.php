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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('website_id')->constrained()->onDelete('cascade');
            $table->bigInteger('wc_order_id')->comment('WooCommerce Order ID');
            $table->string('status')->default('pending'); // e.g., pending, processing, completed, cancelled
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('customer_address')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->json('products')->comment('List of products in the order');
            $table->json('order_data')->comment('Full raw order data from WooCommerce');
            $table->timestamps();

            // To prevent duplicate orders from the same website
            $table->unique(['website_id', 'wc_order_id']);
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
