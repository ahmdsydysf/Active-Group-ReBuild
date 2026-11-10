<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthTest extends TestCase
{
    public function test_health_endpoint_returns_ok(): void
    {
        $this->getJson('/api/v1/health')
            ->assertOk()
            ->assertJson(['status' => 'ok', 'version' => 'v1']);
    }

    public function test_swagger_ui_is_served(): void
    {
        $this->get('/api/documentation')->assertOk();
    }
}
