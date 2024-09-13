<?php

namespace Tests\Feature;

use Tests\TestCase;

class AppTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_redirect_to_documentation_when_get_root_path(): void
    {
        $response = $this->get('/');
        $response->assertStatus(302);
        $response->assertRedirect('/api/docs');
    }
}
