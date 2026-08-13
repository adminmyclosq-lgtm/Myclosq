<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Product;

class ProductApiTest extends TestCase
{
    public function test_products_endpoint_is_public(): void
    {
        Product::create([
            'name' => 'Test Reset',
            'slug' => 'test-reset',
            'status' => 'active',
        ]);

        $this->getJson('/api/v1/products')->assertOk();
    }
}
