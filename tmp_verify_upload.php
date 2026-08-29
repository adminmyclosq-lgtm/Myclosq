<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$product = App\Models\Product::latest('id')->first();
$variant = $product->variants()->first();

$tmp = tempnam(sys_get_temp_dir(), 'img');
file_put_contents($tmp, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAIAAgACABQH0KQAAAAABJRU5ErkJggg=='));
$file = new Illuminate\Http\UploadedFile($tmp, 'verify.png', 'image/png', null, true);

$controller = new App\Http\Controllers\Web\Admin\ProductController();
$method = new ReflectionMethod($controller, 'attachImageToVariant');
$method->setAccessible(true);
$method->invoke($controller, $variant, $file);

echo 'saved_media=' . Illuminate\Support\Facades\DB::table('media')->latest('id')->value('id') . PHP_EOL;
echo 'saved_product_image=' . Illuminate\Support\Facades\DB::table('product_images')->latest('id')->value('id') . PHP_EOL;
unlink($tmp);
