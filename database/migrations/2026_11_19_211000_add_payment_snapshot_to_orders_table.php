<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('payment_method_id')->nullable()->after('subscription_id')->constrained('payment_methods')->nullOnDelete();
            $table->unsignedTinyInteger('payment_type')->nullable()->after('payment_method_id');
            $table->string('payment_brand')->nullable()->after('payment_type');
            $table->string('payment_last_four', 8)->nullable()->after('payment_brand');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_method_id');
            $table->dropColumn(['payment_type', 'payment_brand', 'payment_last_four']);
        });
    }
};
