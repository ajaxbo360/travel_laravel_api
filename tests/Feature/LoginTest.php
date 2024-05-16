<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    // test for check login  credientials

    public function test_check_login_credientials(): void
    {
        $user = User::factory()->create();
        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token']);
    }

    // test user login credientails fail case

    public function test_check_login_credientials_fail(): void
    {

        $response = $this->postJson('/api/v1/login', [
            'email' => "wrong-email",
            'password' => 'wrong-password'
        ]);
        $response->assertStatus(422);
    }
}
