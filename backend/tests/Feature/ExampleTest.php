<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_api_returns_json_on_invalid_route(): void
    {
        $response = $this->getJson('/api/v1/nonexistent');

        $response->assertStatus(404);
    }
}
