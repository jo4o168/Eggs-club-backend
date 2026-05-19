<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('subscriptions', 'payment_method_id')) {
            return;
        }

        try {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropForeign(['payment_method_id']);
            });
        } catch (\Throwable) {
            // Constraint already removed (ex.: migração interrompida antes).
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_method_id')->nullable()->change();
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('subscriptions', 'payment_method_id')) {
            return;
        }

        try {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropForeign(['payment_method_id']);
            });
        } catch (\Throwable) {
            //
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_method_id')->nullable(false)->change();
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
        });
    }
};
