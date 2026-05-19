<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('profiles');
            $table->foreignId('product_id')->constrained('products');
            $table->unsignedInteger('quantity')->default(1);
            $table->string('purchase_mode', 32);
            $table->foreignId('subscription_plan_id')->nullable()->constrained('subscription_plans');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
