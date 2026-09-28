<?php

namespace Tests\Integration;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserRegistrationAndLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_be_created_then_log_in_with_the_created_account(): void
    {
        $credentials = [
            'name' => 'Alice Test',
            'email' => 'alice.test@example.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $registration = $this->post('/register', $credentials);

        $registration->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticated();
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
