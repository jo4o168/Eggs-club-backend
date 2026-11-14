<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('rating');
            $table->string('comment', 500)->nullable();
            $table->foreignId('customer_id')->constrained('profiles');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('order_id')->constrained('orders');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['customer_id', 'order_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
