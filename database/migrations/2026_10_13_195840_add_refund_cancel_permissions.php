<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::table('permissions')->updateOrInsert(
            ['code' => 'ORDER_REFUND'],
            ['name' => 'Refund Orders', 'code' => 'ORDER_REFUND', 'description' => 'Initiate and process order refunds']
        );
        \Illuminate\Support\Facades\DB::table('permissions')->updateOrInsert(
            ['code' => 'ORDER_CANCEL'],
            ['name' => 'Cancel Orders', 'code' => 'ORDER_CANCEL', 'description' => 'Cancel active orders']
        );

        $superAdmin = \Illuminate\Support\Facades\DB::table('roles')->where('code', 'SUPER_ADMIN')->value('id');
        $orderAdmin = \Illuminate\Support\Facades\DB::table('roles')->where('code', 'ORDER_ADMIN')->value('id');

        $permIds = \Illuminate\Support\Facades\DB::table('permissions')->whereIn('code', ['ORDER_REFUND', 'ORDER_CANCEL'])->pluck('id');

        foreach ($permIds as $pid) {
            if ($superAdmin) {
                \Illuminate\Support\Facades\DB::table('permission_role')->updateOrInsert(['role_id' => $superAdmin, 'permission_id' => $pid], []);
            }
            if ($orderAdmin) {
                \Illuminate\Support\Facades\DB::table('permission_role')->updateOrInsert(['role_id' => $orderAdmin, 'permission_id' => $pid], []);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permIds = \Illuminate\Support\Facades\DB::table('permissions')->whereIn('code', ['ORDER_REFUND', 'ORDER_CANCEL'])->pluck('id');
        \Illuminate\Support\Facades\DB::table('permission_role')->whereIn('permission_id', $permIds)->delete();
        \Illuminate\Support\Facades\DB::table('permissions')->whereIn('id', $permIds)->delete();
    }
};
