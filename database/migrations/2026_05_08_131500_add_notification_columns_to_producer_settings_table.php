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
            if (!Schema::hasColumn('producer_settings', 'email_notifications')) {
                $table->boolean('email_notifications')->default(true)->after('visible_in_search');
            }

            if (!Schema::hasColumn('producer_settings', 'sms_notifications')) {
                $table->boolean('sms_notifications')->default(false)->after('email_notifications');
            }

            if (!Schema::hasColumn('producer_settings', 'new_order_alert')) {
                $table->boolean('new_order_alert')->default(true)->after('sms_notifications');
            }

            if (!Schema::hasColumn('producer_settings', 'weekly_report')) {
                $table->boolean('weekly_report')->default(true)->after('new_order_alert');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('producer_settings')) {
            return;
        }

        Schema::table('producer_settings', function (Blueprint $table) {
            $toDrop = [];

            if (Schema::hasColumn('producer_settings', 'email_notifications')) {
                $toDrop[] = 'email_notifications';
            }

            if (Schema::hasColumn('producer_settings', 'sms_notifications')) {
                $toDrop[] = 'sms_notifications';
            }

            if (Schema::hasColumn('producer_settings', 'new_order_alert')) {
                $toDrop[] = 'new_order_alert';
            }

            if (Schema::hasColumn('producer_settings', 'weekly_report')) {
                $toDrop[] = 'weekly_report';
            }

            if (!empty($toDrop)) {
                $table->dropColumn($toDrop);
            }
        });
    }
};
