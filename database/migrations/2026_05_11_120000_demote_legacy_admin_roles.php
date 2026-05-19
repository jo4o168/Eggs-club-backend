<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getSchemaBuilder()->hasTable('profiles')) {
            DB::table('profiles')->where('role', 2)->update(['role' => 0]);
        }

        if (DB::getSchemaBuilder()->hasTable('users')) {
            $users = DB::table('users')->select('id', 'roles')->get();
            foreach ($users as $user) {
                $roles = json_decode($user->roles, true);
                if (! is_array($roles)) {
                    continue;
                }
                $changed = false;
                foreach ($roles as $i => $r) {
                    if ((int) $r === 2) {
                        $roles[$i] = 0;
                        $changed = true;
                    }
                }
                if ($changed) {
                    DB::table('users')->where('id', $user->id)->update(['roles' => json_encode($roles)]);
                }
            }
        }
    }

    public function down(): void
    {
        //
    }
};
