<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\Web\Admin\ProductController;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

function makeTinyPngFile(string $name): UploadedFile {
    $tmp = tempnam(sys_get_temp_dir(), 'img');
    file_put_contents($tmp, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAIAAgACABQH0KQAAAAABJRU5ErkJggg=='));
    return new UploadedFile($tmp, $name, 'image/png', null, true);
}

$slug = 'wf-' . Str::lower(Str::random(8));
$product = DB::transaction(function () use ($slug) {
    $p = Product::create([
        'name' => 'Workflow Verify ' . $slug,
        'slug' => $slug,
        'base_sku' => 'WF-' . strtoupper(Str::random(6)),
        'short_description' => 'verify',
        'description' => 'verify',
        'status' => 'active',
        'is_featured' => false,
    ]);

    $variant = $p->variants()->create([
        'name' => 'Default',
        'sku' => 'WFVAR-' . strtoupper(Str::random(6)),
        'status' => 'active',
    ]);

    $variant->prices()->create([
        'mrp' => 100,
        'selling_price' => 90,
        'tax_percentage' => 0,
        'customer_group' => 'default',
        'effective_from' => now(),
        'is_active' => true,
    ]);

    return $p->fresh('variants');
});

$variant = $product->variants->first();
$controller = new ProductController();
$method = new ReflectionMethod($controller, 'attachImageToVariant');
$method->setAccessible(true);

$f1 = makeTinyPngFile('first.png');
$method->invoke($controller, $variant, $f1);
$firstMediaId = DB::table('product_images')->where('product_variant_id', $variant->id)->value('media_id');

$f2 = makeTinyPngFile('second.png');
$method->invoke($controller, $variant, $f2);
$secondMediaId = DB::table('product_images')->where('product_variant_id', $variant->id)->value('media_id');

$firstStillLinked = DB::table('product_images')->where('media_id', $firstMediaId)->exists();
$firstMediaExists = DB::table('media')->where('id', $firstMediaId)->exists();
$currentImageCount = DB::table('product_images')->where('product_variant_id', $variant->id)->count();

echo 'first_media_id=' . $firstMediaId . PHP_EOL;
echo 'second_media_id=' . $secondMediaId . PHP_EOL;
echo 'first_still_linked=' . ($firstStillLinked ? 'yes' : 'no') . PHP_EOL;
echo 'first_media_exists=' . ($firstMediaExists ? 'yes' : 'no') . PHP_EOL;
echo 'current_variant_image_count=' . $currentImageCount . PHP_EOL;
