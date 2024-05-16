<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTourTest extends TestCase
{
    use RefreshDatabase;

    // test public users to access non-public travel
    public function test_public_users_cannot_access_adding_tours(): void
    {
        $travel = Travel::factory()->create();
        $response = $this->postJson('/api/v1/admin/travels/' . $travel->id . '/tours');
        $response->assertStatus(401);
    }

    // test non admin users to access adding travel

    public function test_non_admin_users_cannot_access_adding_tours(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'editor')->value('id'));
        $travel = Travel::factory()->create();
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/' . $travel->id . '/tours');
        $response->assertStatus(403);
    }
    public function test_saves_tours_correctly_with_valid_data(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'admin')->value('id'));
        $travel = Travel::factory()->create();
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/' . $travel->id . '/tours', [
            'name' => 'Travel name',
        ]);
        $response->assertStatus(422);
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/' . $travel->id . '/tours', [
            'name' => 'Travel name',
            'is_public' => 1,
            'description' => 'Travel description',
            'number_of_days' => 10,
        ]);
        $response->assertStatus(201);
        $response->assertJsonFragment(['name' => 'Travel name']);
    }
}
