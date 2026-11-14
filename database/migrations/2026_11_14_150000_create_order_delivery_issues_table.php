<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_delivery_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->unique();
            $table->foreignId('customer_id')->constrained('profiles');
            $table->string('reason', 32);
            $table->string('description', 1000);
            $table->string('status', 32)->default('open');
            $table->string('resolution_message', 1000)->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_delivery_issues');
    }
};
