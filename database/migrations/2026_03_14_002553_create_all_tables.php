<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('producer_settings', function (Blueprint $table) {
            $table->id();
            $table->string('farm_name');
            $table->string('description')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('delivery_info')->nullable();
            $table->boolean('accepts_new_subscribers')->default(true);
            $table->boolean('visible_in_search')->default(true);
            $table->foreignId('producer_id')->constrained('profiles');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('eggs_quantity');
            $table->integer('frequency')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->foreignId('producer_id')->constrained('profiles');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(0);
            $table->date('start_date');
            $table->date('next_delivery_date')->nullable();
            $table->date('pause_until')->nullable();
            $table->foreignId('customer_id')->constrained('profiles');
            $table->foreignId('producer_id')->constrained('profiles');
            $table->foreignId('subscription_plan_id')->nullable()->constrained('subscription_plans');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('delivery_address')->nullable();
            $table->string('notes')->nullable();
            $table->integer('status')->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->foreignId('customer_id')->constrained('profiles');
            $table->foreignId('producer_id')->constrained('profiles');
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('product_id')->nullable()->constrained('products');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->integer('type');
            $table->string('card_last_four')->nullable();
            $table->string('card_brand')->nullable();
            $table->boolean('is_default')->default(false);
            $table->foreignId('customer_id')->constrained('profiles');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_plans');
        Schema::dropIfExists('producer_settings');
    }
};
