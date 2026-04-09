<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();
            $table->json('gateway_response')->nullable();
            $table->integer('status');
            $table->tinyInteger('installments');
            $table->decimal('amount', 10, 2);
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('payment_method_id')->constrained('payment_methods');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
