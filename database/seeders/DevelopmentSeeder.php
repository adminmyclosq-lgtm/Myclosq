<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            return;
        }

        $admin = User::firstOrCreate(
            ['email'=>'admin@gutreset.local'],
            [
                'uuid'=>(string) Str::uuid(),
                'password_hash'=>Hash::make('ChangeMe!123'),
                'status'=>'active',
            ]
        );

        $adminRole = DB::table('roles')->where('code','SUPER_ADMIN')->value('id');
        if ($adminRole) {
            $admin->roles()->syncWithoutDetaching([$adminRole]);
        }
        $admin->customerProfile()->updateOrCreate([], ['first_name'=>'Gut Reset','last_name'=>'Admin','display_name'=>'Gut Reset Admin']);

        $customer = User::firstOrCreate(
            ['email'=>'customer@gutreset.local'],
            [
                'uuid'=>(string) Str::uuid(),
                'password_hash'=>Hash::make('ChangeMe!123'),
                'status'=>'active',
            ]
        );

        $customerRole = DB::table('roles')->where('code','CUSTOMER')->value('id');
        if ($customerRole) {
            $customer->roles()->syncWithoutDetaching([$customerRole]);
        }
        $customer->customerProfile()->updateOrCreate([], ['first_name'=>'Demo','last_name'=>'Customer','display_name'=>'Demo Customer']);
    }
}
