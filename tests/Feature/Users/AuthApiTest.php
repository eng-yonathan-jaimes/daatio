<?php

namespace Tests\Feature\Users;

use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Modules\Users\app\Models\User;

class AuthApiTest extends TestCase
{
    public function test_user_can_register_login_read_profile_and_logout(): void
    {
        $email = 'auth-test+'.uniqid().'@example.com';

        $registerResponse = $this->postJson('/api/auth/register', [
            'tenant_id' => 1,
            'user_name' => 'Ada',
            'user_lastName' => 'Lovelace',
            'user_email' => $email,
            'user_access' => 'owner',
            'user_password' => 'password123',
            'user_password_confirmation' => 'password123',
            'user_phone_number' => '+10000000000',
        ]);

        $registerResponse
            ->assertCreated()
            ->assertJsonStructure([
                'token',
                'user' => [
                    'id',
                    'tenant_id',
                    'user_name',
                    'user_lastName',
                    'user_email',
                    'user_access',
                    'user_phone_number',
                    'user_creation',
                    'user_update_date',
                ],
            ]);

        $this->assertDatabaseHas('user', [
            'user_email' => $email,
        ]);

        $loginResponse = $this->postJson('/api/auth/login', [
            'user_email' => $email,
            'user_password' => 'password123',
        ]);

        $loginResponse
            ->assertOk()
            ->assertJsonStructure(['token', 'user']);

        $token = $loginResponse->json('token');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('user.user_email', $email);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/auth/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Logged out successfully.');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/auth/me')
            ->assertUnauthorized();
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $email = 'auth-fail+'.uniqid().'@example.com';

        User::create([
            'tenant_id' => 1,
            'user_name' => 'Grace',
            'user_lastName' => 'Hopper',
            'user_email' => $email,
            'user_access' => 'owner',
            'user_password' => Hash::make('password123'),
            'user_phone_number' => '+10000000001',
            'user_update_date' => now(),
        ]);

        $this->postJson('/api/auth/login', [
            'user_email' => $email,
            'user_password' => 'wrong-password',
        ])->assertUnprocessable();
    }
}
