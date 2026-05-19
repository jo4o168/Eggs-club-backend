<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Contas já existentes antes da verificação obrigatória de e-mail.
     */
    public function up(): void
    {
        if (DB::getSchemaBuilder()->hasTable('users')) {
            DB::table('users')->whereNull('email_verified_at')->update(['email_verified_at' => now()]);
        }
    }

    public function down(): void
    {
        //
    }
};
