<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$names = [
    'Gut Reset Daily Capsules',
    'Gut Reset Probiotic Blend',
    'Gut Reset Morning Tea',
];

foreach ($names as $name) {
    $product = App\Models\Product::where('name', $name)->first();
    if (!$product) {
        echo $name . ': missing product' . PHP_EOL;
        continue;
    }

    $image = $product->productImages()->with('media')->first();
    if (!$image || !$image->media) {
        echo $name . ': missing image link' . PHP_EOL;
        continue;
    }

    echo $name . ': ' . $image->media->storage_path . PHP_EOL;
}
