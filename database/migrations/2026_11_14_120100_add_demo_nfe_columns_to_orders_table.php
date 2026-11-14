<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('nfe_access_key', 44)->nullable()->after('producer_message');
            $table->unsignedInteger('nfe_number')->nullable()->after('nfe_access_key');
            $table->timestamp('nfe_issued_at')->nullable()->after('nfe_number');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['nfe_access_key', 'nfe_number', 'nfe_issued_at']);
        });
    }
};
