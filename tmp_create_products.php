<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$products = [
    [
        'name' => 'Gut Reset Daily Capsules',
        'slug' => 'gut-reset-daily-capsules',
        'base_sku' => 'GR-001',
        'short_description' => 'Daily capsule support for gut comfort and routine.',
        'description' => 'A daily capsule blend designed to support digestion and consistency.',
        'status' => 'active',
        'is_featured' => true,
    ],
    [
        'name' => 'Gut Reset Probiotic Blend',
        'slug' => 'gut-reset-probiotic-blend',
        'base_sku' => 'GR-002',
        'short_description' => 'A probiotic-forward blend for everyday digestion.',
        'description' => 'A gentle probiotic blend crafted for daily gut balance.',
        'status' => 'active',
        'is_featured' => false,
    ],
    [
        'name' => 'Gut Reset Morning Tea',
        'slug' => 'gut-reset-morning-tea',
        'base_sku' => 'GR-003',
        'short_description' => 'A calming herbal tea for morning gut support.',
        'description' => 'A soothing herbal tea designed for a gentle start to the day.',
        'status' => 'active',
        'is_featured' => false,
    ],
];

foreach ($products as $productData) {
    $product = App\Models\Product::create($productData);
    $variant = $product->variants()->create([
        'name' => $product->name,
        'sku' => strtoupper(str_replace('-', '', $product->slug)) . '-01',
        'status' => 'active',
    ]);
    $variant->prices()->create([
        'mrp' => 1299,
        'selling_price' => 999,
        'tax_percentage' => 0,
        'customer_group' => 'default',
        'effective_from' => now(),
        'is_active' => true,
    ]);
}

echo "Created " . count($products) . " products.\n";
