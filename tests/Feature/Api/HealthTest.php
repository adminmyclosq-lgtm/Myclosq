<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class HealthTest extends TestCase
{
    public function test_home_endpoint_is_reachable(): void
    {
        $this->getJson('/api/v1/home')->assertOk();
    }
}
