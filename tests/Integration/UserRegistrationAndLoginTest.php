<?php

namespace Tests\Integration;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserRegistrationAndLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_administrator_can_create_a_user_who_can_then_log_in(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Administrateur]);
        $credentials = [
            'first_name' => 'Alice',
            'last_name' => 'Test',
            'email' => 'alice.test@example.test',
            'position' => 'Employée',
            'department' => 'Informatique',
            'role' => UserRole::Personnel->value,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $creation = $this->actingAs($admin)->post(route('admin.users.store'), $credentials);

        $creation->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Alice Test',
            'email' => 'alice.test@example.test',
        ]);
        $this->assertTrue(Hash::check('Password123!', User::where('email', $credentials['email'])->firstOrFail()->password));

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();

        $login = $this->post('/login', [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ]);

        $login->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs(User::where('email', $credentials['email'])->firstOrFail());
    }
}
