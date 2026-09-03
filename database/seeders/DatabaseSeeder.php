<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name'=>'Super Admin','code'=>'SUPER_ADMIN','description'=>'Full system administration'],
            ['name'=>'Content Admin','code'=>'CONTENT_ADMIN','description'=>'CMS and content administration'],
            ['name'=>'Product Admin','code'=>'PRODUCT_ADMIN','description'=>'Product and catalogue administration'],
            ['name'=>'Order Admin','code'=>'ORDER_ADMIN','description'=>'Order and payment administration'],
            ['name'=>'Fulfilment Admin','code'=>'FULFILMENT_ADMIN','description'=>'Shipment and fulfilment administration'],
            ['name'=>'Customer Support','code'=>'CUSTOMER_SUPPORT','description'=>'Customer support operations'],
            ['name'=>'Marketing Admin','code'=>'MARKETING_ADMIN','description'=>'Marketing and commercial operations'],
            ['name'=>'Reset Admin','code'=>'RESET_ADMIN','description'=>'30-day reset operations'],
            ['name'=>'Analyst','code'=>'ANALYST','description'=>'Reporting and analytics'],
            ['name'=>'Customer','code'=>'CUSTOMER','description'=>'Standard storefront customer'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(['code' => $role['code']], $role);
        }

        $permissions = [
            ['name'=>'View Users','code'=>'USER_VIEW','description'=>'View users and customer profiles'],
            ['name'=>'Manage Users','code'=>'USER_MANAGE','description'=>'Create and update users'],
            ['name'=>'View Products','code'=>'PRODUCT_VIEW','description'=>'View products and catalogue'],
            ['name'=>'Manage Products','code'=>'PRODUCT_MANAGE','description'=>'Create and update products'],
            ['name'=>'View Orders','code'=>'ORDER_VIEW','description'=>'View orders'],
            ['name'=>'Manage Orders','code'=>'ORDER_MANAGE','description'=>'Manage order lifecycle'],
            ['name'=>'View Payments','code'=>'PAYMENT_VIEW','description'=>'View payment transactions'],
            ['name'=>'Manage Inventory','code'=>'INVENTORY_MANAGE','description'=>'Manage inventory'],
            ['name'=>'Manage CMS','code'=>'CMS_MANAGE','description'=>'Manage CMS pages and sections'],
            ['name'=>'View Reset Profiles','code'=>'RESET_VIEW','description'=>'View reset journeys'],
            ['name'=>'Manage Reset Profiles','code'=>'RESET_MANAGE','description'=>'Manage reset journeys'],
            ['name'=>'Review Safety Flags','code'=>'SAFETY_REVIEW','description'=>'Review safety flags'],
            ['name'=>'View Analytics','code'=>'ANALYTICS_VIEW','description'=>'View operational and response analytics'],
            ['name'=>'Manage WhatsApp','code'=>'WHATSAPP_MANAGE','description'=>'Manage WhatsApp messaging'],
            ['name'=>'View Audit Logs','code'=>'AUDIT_VIEW','description'=>'View audit logs'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(['code' => $permission['code']], $permission);
        }

        $superAdmin = DB::table('roles')->where('code','SUPER_ADMIN')->value('id');
        $customer = DB::table('roles')->where('code','CUSTOMER')->value('id');

        foreach (DB::table('permissions')->pluck('id') as $permissionId) {
            DB::table('permission_role')->updateOrInsert([
                'role_id' => $superAdmin,
                'permission_id' => $permissionId,
            ], []);
        }

        foreach (['PRODUCT_VIEW','ORDER_VIEW','RESET_VIEW'] as $code) {
            $permissionId = DB::table('permissions')->where('code',$code)->value('id');
            DB::table('permission_role')->updateOrInsert([
                'role_id' => $customer,
                'permission_id' => $permissionId,
            ], []);
        }

        DB::table('shipping_methods')->updateOrInsert(
            ['code'=>'STANDARD'],
            [
                'name'=>'Standard Delivery',
                'courier'=>null,
                'description'=>'Standard ecommerce delivery',
                'estimated_min_days'=>3,
                'estimated_max_days'=>7,
                'is_active'=>1,
                'sort_order'=>1,
                'created_at'=>now(),
                'updated_at'=>now(),
            ]
        );

        DB::table('shipping_methods')->updateOrInsert(
            ['code'=>'EXPRESS'],
            [
                'name'=>'Express Delivery',
                'courier'=>null,
                'description'=>'Express ecommerce delivery',
                'estimated_min_days'=>1,
                'estimated_max_days'=>3,
                'is_active'=>1,
                'sort_order'=>2,
                'created_at'=>now(),
                'updated_at'=>now(),
            ]
        );

        DB::table('system_settings')->updateOrInsert(
            ['setting_group'=>'site','setting_key'=>'site_name'],
            ['setting_value'=>'Gut Reset','value_type'=>'string','is_public'=>1,'is_encrypted'=>0,'description'=>'Public site name','updated_at'=>now()]
        );

        DB::table('system_settings')->updateOrInsert(
            ['setting_group'=>'site','setting_key'=>'default_currency'],
            ['setting_value'=>'INR','value_type'=>'string','is_public'=>1,'is_encrypted'=>0,'description'=>'Default ecommerce currency','updated_at'=>now()]
        );

        DB::table('system_settings')->updateOrInsert(
            ['setting_group'=>'reset','setting_key'=>'reset_duration_days'],
            ['setting_value'=>'30','value_type'=>'integer','is_public'=>0,'is_encrypted'=>0,'description'=>'Duration of guided reset','updated_at'=>now()]
        );


        $templates = [
            ['name'=>'order_paid','meta_template_name'=>'gutreset_order_paid','language_code'=>'en','category'=>'UTILITY','body'=>'Your Gut Reset order {{1}} is paid. Total ₹{{2}}.','status'=>'draft'],
            ['name'=>'order_packed','meta_template_name'=>'gutreset_order_packed','language_code'=>'en','category'=>'UTILITY','body'=>'Your Gut Reset order {{1}} has been packed.','status'=>'draft'],
            ['name'=>'order_dispatched','meta_template_name'=>'gutreset_order_dispatched','language_code'=>'en','category'=>'UTILITY','body'=>'Your Gut Reset order {{1}} is on the way. Tracking: {{2}}','status'=>'draft'],
            ['name'=>'order_out_for_delivery','meta_template_name'=>'gutreset_order_out_for_delivery','language_code'=>'en','category'=>'UTILITY','body'=>'Your Gut Reset order {{1}} is out for delivery. Tracking: {{2}}','status'=>'draft'],
            ['name'=>'order_delivered','meta_template_name'=>'gutreset_order_delivered','language_code'=>'en','category'=>'UTILITY','body'=>'Your Gut Reset order {{1}} has been delivered.','status'=>'draft'],
            ['name'=>'daily_checkin','meta_template_name'=>'gutreset_daily_checkin','language_code'=>'en','category'=>'UTILITY','body'=>'Gut Reset Day {{1}} check-in: reply YES when your check-in is complete.','status'=>'draft'],
            ['name'=>'daily_followup','meta_template_name'=>'gutreset_daily_followup','language_code'=>'en','category'=>'UTILITY','body'=>'Thanks for completing Day {{1}}. Keep going with your Gut Reset journey.','status'=>'draft'],
            ['name'=>'day30_complete','meta_template_name'=>'gutreset_day30_complete','language_code'=>'en','category'=>'UTILITY','body'=>'Your 30-day Gut Reset is complete. Your Day-30 response summary is ready for review.','status'=>'draft'],
        ];
        foreach ($templates as $template) {
            DB::table('whatsapp_templates')->updateOrInsert(
                ['name'=>$template['name'],'language_code'=>$template['language_code']],
                $template + ['is_active'=>1,'created_at'=>now(),'updated_at'=>now()]
            );
        }

        $this->call(DevelopmentSeeder::class);
        $this->call(HowItWorksPageSeeder::class);
    }
}
