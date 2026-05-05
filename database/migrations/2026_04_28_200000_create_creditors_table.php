<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creditors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_owner_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->string('customer_name');
            $table->string('customer_phone', 30)->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->string('discount_type', 20)->nullable();
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->default(0);
            $table->string('status', 20)->default('unpaid'); // unpaid|paid
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('paid_at')->nullable();

            $table->foreign('sale_id')->references('id')->on('sales')->nullOnDelete();
        });

        Schema::create('creditor_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creditor_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('quantity');
            $table->decimal('price', 10, 2);

            $table->foreign('creditor_id')->references('id')->on('creditors')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creditor_items');
        Schema::dropIfExists('creditors');
    }
};
