<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTravelTest extends TestCase
{
    use RefreshDatabase;

    // test public users to access non-public travel
    public function test_public_users_cannot_access_adding_travels(): void
    {

        $response = $this->postJson('/api/v1/admin/travels');
        $response->assertStatus(401);
    }

    // test non admin users to access adding travel

    public function test_non_admin_users_cannot_access_adding_travels(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'editor')->value('id'));
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels');
        $response->assertStatus(403);
    }
    public function test_saves_travels_correctly_with_valid_data(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'admin')->value('id'));
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels', [
            'name' => 'Travel name',
        ]);
        $response->assertStatus(422);
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels', [
            'name' => 'Travel name',
            'is_public' => 1,
            'description' => 'Travel description',
            'number_of_days' => 10,
        ]);
        $response->assertStatus(201);
        $response->assertJsonFragment(['name' => 'Travel name']);
    }
    // test admin and editor users can update the travel record

    public function test_admin_and_editor_users_can_update_the_travel_record(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'editor')->value('id'));
        $travel = Travel::factory()->create();
        $response = $this->actingAs($user)->putJson('/api/v1/admin/travels/' . $travel->id, [
            'name' => 'Travel name',
        ]);
        $response->assertStatus(422);
        $response = $this->actingAs($user)->putJson('/api/v1/admin/travels/' . $travel->id, [
            'name' => 'Travel name updated',
            'is_public' => 1,
            'description' => 'Travel description',
            'number_of_days' => 10,
        ]);
        $response->assertStatus(200);
        $response = $this->get('/api/v1/travels');
        $response->assertJsonFragment(['name' => 'Travel name updated']);
    }
}
