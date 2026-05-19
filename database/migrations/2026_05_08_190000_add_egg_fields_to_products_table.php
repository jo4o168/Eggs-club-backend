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
            if (!Schema::hasColumn('products', 'egg_size')) {
                $table->string('egg_size')->nullable()->after('name');
            }

            if (!Schema::hasColumn('products', 'egg_color')) {
                $table->string('egg_color')->nullable()->after('egg_size');
            }

            if (!Schema::hasColumn('products', 'kit_quantity')) {
                $table->integer('kit_quantity')->nullable()->after('egg_color');
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

            if (Schema::hasColumn('products', 'egg_size')) {
                $toDrop[] = 'egg_size';
            }

            if (Schema::hasColumn('products', 'egg_color')) {
                $toDrop[] = 'egg_color';
            }

            if (Schema::hasColumn('products', 'kit_quantity')) {
                $toDrop[] = 'kit_quantity';
            }

            if (!empty($toDrop)) {
                $table->dropColumn($toDrop);
            }
        });
    }
};
