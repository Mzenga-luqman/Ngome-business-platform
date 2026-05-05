<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_includes_security_headers(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->assertHeader('Content-Security-Policy');
    }

    public function test_login_endpoint_is_rate_limited(): void
    {
        $payload = [
            'email' => 'nobody@example.com',
            'password' => 'invalid-password',
        ];

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', $payload)->assertStatus(302);
        }

        $this->post('/login', $payload)->assertStatus(429);
    }

    public function test_register_endpoint_is_rate_limited(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->post('/register', [
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@example.com',
                'phone' => '07123456' . $i,
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])->assertStatus(302);
        }

        $this->post('/register', [
            'name' => 'Rate Limited User',
            'email' => 'ratelimit@example.com',
            'phone' => '0799999999',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(429);
    }
}
