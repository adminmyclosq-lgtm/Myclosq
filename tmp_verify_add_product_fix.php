<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\Web\Admin\ProductController;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

$slug = 'verify-add-' . Str::lower(Str::random(8));
$tmp = tempnam(sys_get_temp_dir(), 'img');
file_put_contents($tmp, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAIAAgACABQH0KQAAAAABJRU5ErkJggg=='));
$imageFile = new UploadedFile($tmp, 'verify.png', 'image/png', null, true);

try {
    DB::transaction(function () use ($slug, $imageFile) {
        $data = [
            'name' => 'Verify Add Product ' . $slug,
            'slug' => $slug,
            'base_sku' => 'VRF-' . strtoupper(Str::random(6)),
            'short_description' => 'Verification product',
            'description' => 'Verification product',
            'status' => 'active',
            'is_featured' => false,
            'image' => $imageFile,
            'variant' => [
                'name' => 'Default',
                'sku' => 'VRFVAR-' . strtoupper(Str::random(6)),
                'status' => 'active',
            ],
            'price' => [
                'mrp' => 1000,
                'selling_price' => 900,
                'tax_percentage' => 0,
                'customer_group' => 'default',
            ],
        ];

        $image = $data['image'] ?? null;
        $variantData = $data['variant'];
        unset($data['variant']);
        $priceData = $data['price'];
        unset($data['price']);
        unset($data['image']);

        $product = Product::create($data);
        $variant = $product->variants()->create($variantData);
        $variant->prices()->create(array_merge($priceData, ['effective_from' => now(), 'is_active' => true]));

        if ($image instanceof UploadedFile) {
            $controller = new ProductController();
            $method = new ReflectionMethod($controller, 'attachImageToVariant');
            $method->setAccessible(true);
            $method->invoke($controller, $variant, $image);
        }

        echo 'created_product_id=' . $product->id . PHP_EOL;
    });
} finally {
    if (is_file($tmp)) {
        @unlink($tmp);
    }
}
