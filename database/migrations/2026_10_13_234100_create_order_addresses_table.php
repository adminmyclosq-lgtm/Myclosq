<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('customer_address_id')->nullable()->constrained('customer_addresses')->nullOnDelete();
            $table->string('type', 20)->default('shipping'); // 'shipping' or 'billing'
            
            // Core identity
            $table->string('full_name', 150);
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            
            // Street / locality
            $table->string('address_line1', 255);
            $table->string('address_line2', 255)->nullable();
            $table->string('landmark', 150)->nullable();
            $table->string('area', 100)->nullable();
            
            // Geographic hierarchy
            $table->string('city', 100);
            $table->string('district', 100)->nullable();
            $table->string('state', 100);
            $table->string('postal_code', 20);
            $table->string('country', 100)->default('India');
            
            // Map Coordinates
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Logistical Metadata
            $table->string('delivery_instructions', 500)->nullable();
            $table->string('address_type', 20)->default('home'); // home, office, other
            $table->boolean('is_verified')->default(false);
            $table->json('raw_payload')->nullable();

            $table->timestamps();

            // High-performance searching and analytics indexes
            $table->index(['order_id', 'type'], 'idx_order_addr_order_type');
            $table->index('country', 'idx_order_addr_country');
            $table->index('state', 'idx_order_addr_state');
            $table->index('city', 'idx_order_addr_city');
            $table->index('district', 'idx_order_addr_district');
            $table->index('area', 'idx_order_addr_area');
            $table->index('postal_code', 'idx_order_addr_postal_code');
            $table->index(['country', 'state', 'city'], 'idx_order_addr_country_state_city');
            $table->index(['state', 'city'], 'idx_order_addr_state_city');
        });

        // Backfill existing orders from shipping_address_json and billing_address_json
        $orders = DB::table('orders')->select('id', 'shipping_address_json', 'billing_address_json', 'created_at')->get();
        foreach ($orders as $order) {
            $shipping = is_string($order->shipping_address_json) 
                ? json_decode($order->shipping_address_json, true) 
                : $order->shipping_address_json;

            if (is_array($shipping) && !empty($shipping)) {
                DB::table('order_addresses')->insert([
                    'order_id' => $order->id,
                    'customer_address_id' => null,
                    'type' => 'shipping',
                    'full_name' => $shipping['recipient_name'] ?? ($shipping['full_name'] ?? 'Customer'),
                    'phone' => $shipping['phone'] ?? null,
                    'email' => $shipping['email'] ?? null,
                    'address_line1' => $shipping['address_line1'] ?? 'Address line 1',
                    'address_line2' => $shipping['address_line2'] ?? null,
                    'landmark' => $shipping['landmark'] ?? null,
                    'area' => $shipping['area'] ?? null,
                    'city' => $shipping['city'] ?? 'City',
                    'district' => $shipping['district'] ?? null,
                    'state' => $shipping['state'] ?? 'State',
                    'postal_code' => $shipping['postal_code'] ?? '000000',
                    'country' => $shipping['country'] ?? 'India',
                    'latitude' => isset($shipping['latitude']) ? (float)$shipping['latitude'] : null,
                    'longitude' => isset($shipping['longitude']) ? (float)$shipping['longitude'] : null,
                    'delivery_instructions' => $shipping['delivery_instructions'] ?? null,
                    'address_type' => $shipping['address_type'] ?? 'home',
                    'is_verified' => !empty($shipping['is_verified']),
                    'raw_payload' => json_encode($shipping),
                    'created_at' => $order->created_at ?: now(),
                    'updated_at' => $order->created_at ?: now(),
                ]);
            }

            $billing = is_string($order->billing_address_json) 
                ? json_decode($order->billing_address_json, true) 
                : $order->billing_address_json;

            if (is_array($billing) && !empty($billing)) {
                DB::table('order_addresses')->insert([
                    'order_id' => $order->id,
                    'customer_address_id' => null,
                    'type' => 'billing',
                    'full_name' => $billing['recipient_name'] ?? ($billing['full_name'] ?? 'Customer'),
                    'phone' => $billing['phone'] ?? null,
                    'email' => $billing['email'] ?? null,
                    'address_line1' => $billing['address_line1'] ?? 'Address line 1',
                    'address_line2' => $billing['address_line2'] ?? null,
                    'landmark' => $billing['landmark'] ?? null,
                    'area' => $billing['area'] ?? null,
                    'city' => $billing['city'] ?? 'City',
                    'district' => $billing['district'] ?? null,
                    'state' => $billing['state'] ?? 'State',
                    'postal_code' => $billing['postal_code'] ?? '000000',
                    'country' => $billing['country'] ?? 'India',
                    'latitude' => isset($billing['latitude']) ? (float)$billing['latitude'] : null,
                    'longitude' => isset($billing['longitude']) ? (float)$billing['longitude'] : null,
                    'delivery_instructions' => $billing['delivery_instructions'] ?? null,
                    'address_type' => $billing['address_type'] ?? 'home',
                    'is_verified' => !empty($billing['is_verified']),
                    'raw_payload' => json_encode($billing),
                    'created_at' => $order->created_at ?: now(),
                    'updated_at' => $order->created_at ?: now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_addresses');
    }
};
