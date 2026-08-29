<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

$images = [
    [
        'name' => 'Gut Reset Daily Capsules',
        'file' => 'gut-reset-daily-capsules.svg',
        'alt' => 'Gut Reset Daily Capsules product image',
        'content' => '<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="1200" viewBox="0 0 1200 1200"><rect width="1200" height="1200" fill="#f7efe6"/><circle cx="600" cy="430" r="220" fill="#8b5e3c"/><rect x="320" y="660" width="560" height="180" rx="30" fill="#b9895e"/><text x="600" y="760" text-anchor="middle" font-family="Arial, sans-serif" font-size="54" fill="#fff">Daily Capsules</text></svg>',
    ],
    [
        'name' => 'Gut Reset Probiotic Blend',
        'file' => 'gut-reset-probiotic-blend.svg',
        'alt' => 'Gut Reset Probiotic Blend product image',
        'content' => '<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="1200" viewBox="0 0 1200 1200"><rect width="1200" height="1200" fill="#edf6f2"/><rect x="340" y="270" width="520" height="560" rx="50" fill="#4f8f72"/><rect x="408" y="338" width="384" height="424" rx="34" fill="#ffffff"/><text x="600" y="560" text-anchor="middle" font-family="Arial, sans-serif" font-size="54" fill="#2f5f4c">Probiotic Blend</text></svg>',
    ],
    [
        'name' => 'Gut Reset Morning Tea',
        'file' => 'gut-reset-morning-tea.svg',
        'alt' => 'Gut Reset Morning Tea product image',
        'content' => '<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="1200" viewBox="0 0 1200 1200"><rect width="1200" height="1200" fill="#f6f2e8"/><rect x="420" y="220" width="360" height="600" rx="45" fill="#7a8b2e"/><rect x="470" y="280" width="260" height="470" rx="30" fill="#ffffff"/><text x="600" y="520" text-anchor="middle" font-family="Arial, sans-serif" font-size="54" fill="#5f6b22">Morning Tea</text></svg>',
    ],
];

Storage::disk('public')->makeDirectory('products');

foreach ($images as $image) {
    $product = Product::where('name', $image['name'])->firstOrFail();
    $variant = $product->variants()->firstOrFail();
    $path = 'products/' . $image['file'];
    Storage::disk('public')->put($path, $image['content']);

    $mediaId = DB::table('media')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'file_name' => $image['file'],
        'storage_path' => $path,
        'mime_type' => 'image/svg+xml',
        'file_size' => strlen($image['content']),
        'width' => 1200,
        'height' => 1200,
        'alt_text' => $image['alt'],
        'uploaded_by' => null,
        'created_at' => now(),
    ]);

    $media = \App\Models\Media::find($mediaId);

    $variant->images()->create([
        'media_id' => $media->id,
        'image_type' => 'gallery',
        'sort_order' => 0,
        'alt_text' => $image['alt'],
        'is_primary' => true,
    ]);
}

echo "Attached " . count($images) . " images.\n";
