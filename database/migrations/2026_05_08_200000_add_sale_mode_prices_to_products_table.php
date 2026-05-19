<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'subscription_price')) {
                $table->decimal('subscription_price', 10, 2)->nullable()->after('price');
            }

            if (!Schema::hasColumn('products', 'one_time_price')) {
                $table->decimal('one_time_price', 10, 2)->nullable()->after('subscription_price');
            }

            if (!Schema::hasColumn('products', 'allow_subscription')) {
                $table->boolean('allow_subscription')->default(true)->after('one_time_price');
            }

            if (!Schema::hasColumn('products', 'allow_one_time_purchase')) {
                $table->boolean('allow_one_time_purchase')->default(true)->after('allow_subscription');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $toDrop = [];

            if (Schema::hasColumn('products', 'subscription_price')) {
                $toDrop[] = 'subscription_price';
            }
            if (Schema::hasColumn('products', 'one_time_price')) {
                $toDrop[] = 'one_time_price';
            }
            if (Schema::hasColumn('products', 'allow_subscription')) {
                $toDrop[] = 'allow_subscription';
            }
            if (Schema::hasColumn('products', 'allow_one_time_purchase')) {
                $toDrop[] = 'allow_one_time_purchase';
            }

            if (!empty($toDrop)) {
                $table->dropColumn($toDrop);
            }
        });
    }
};
