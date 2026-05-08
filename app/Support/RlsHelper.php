<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class RlsHelper
{
    public static function enable(string $table): void
    {
        DB::statement("ALTER TABLE {$table} ENABLE ROW LEVEL SECURITY");
        DB::statement("ALTER TABLE {$table} FORCE ROW LEVEL SECURITY");

        DB::statement("
            CREATE POLICY tenant_isolation ON {$table}
            USING (empresa_id = current_setting('app.tenant_id', true)::uuid)
            WITH CHECK (empresa_id = current_setting('app.tenant_id', true)::uuid)
        ");
    }

    public static function disable(string $table): void
    {
        DB::statement("DROP POLICY IF EXISTS tenant_isolation ON {$table}");
        DB::statement("ALTER TABLE {$table} DISABLE ROW LEVEL SECURITY");
    }
}
