<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('producer_settings')) {
            return;
        }

        Schema::table('producer_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('producer_settings', 'website')) {
                $table->string('website')->nullable()->after('state');
            }

            if (!Schema::hasColumn('producer_settings', 'certifications')) {
                $table->string('certifications')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('producer_settings')) {
            return;
        }

        Schema::table('producer_settings', function (Blueprint $table) {
            if (Schema::hasColumn('producer_settings', 'website')) {
                $table->dropColumn('website');
            }

            if (Schema::hasColumn('producer_settings', 'certifications')) {
                $table->dropColumn('certifications');
            }
        });
    }
};
