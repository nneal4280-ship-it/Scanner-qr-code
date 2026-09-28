<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_create_a_user_and_its_profile(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Administrateur]);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'first_name' => 'Marie',
            'last_name' => 'Fouda',
            'email' => 'marie.fouda@example.test',
            'position' => 'Employée',
            'department' => 'Informatique',
            'role' => UserRole::Personnel->value,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['email' => 'marie.fouda@example.test', 'name' => 'Marie Fouda']);
        $this->assertDatabaseHas('profiles', ['first_name' => 'Marie', 'last_name' => 'Fouda', 'position' => 'Employée', 'department' => 'Informatique']);
        $this->assertTrue(Hash::check('Password123!', User::where('email', 'marie.fouda@example.test')->firstOrFail()->password));
    }

    public function test_only_an_administrator_can_access_user_creation(): void
    {
        $user = User::factory()->create(['role' => UserRole::Personnel]);

        $this->assertFalse($user->can('create', User::class));
        $this->assertTrue(User::factory()->create(['role' => UserRole::Administrateur])->can('create', User::class));
    }
}
