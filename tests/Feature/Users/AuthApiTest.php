<?php

namespace Tests\Feature\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Modules\Users\app\Models\User;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    private function validRegisterPayload(array $overrides = []): array
    {
        return array_merge([
            'tenant_id' => 1,
            'user_name' => 'Ada',
            'user_lastName' => 'Lovelace',
            'user_email' => 'ada+'.uniqid().'@example.com',
            'user_access' => 'owner',
            'user_password' => 'password123',
            'user_password_confirmation' => 'password123',
            'user_phone_number' => '+10000000000',
        ], $overrides);
    }

    private function createUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'tenant_id' => 1,
            'user_name' => 'Grace',
            'user_lastName' => 'Hopper',
            'user_email' => 'grace+'.uniqid().'@example.com',
            'user_access' => 'owner',
            'user_password' => Hash::make('password123'),
            'user_phone_number' => '+10000000001',
            'user_update_date' => now(),
        ], $overrides));
    }

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

        $loginTokenId = explode('|', $token)[0];

        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $loginTokenId,
        ]);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/auth/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Logged out successfully.');

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $loginTokenId,
        ]);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = $this->createUser();

        $this->postJson('/api/auth/login', [
            'user_email' => $user->user_email,
            'user_password' => 'wrong-password',
        ])->assertUnprocessable();
    }

    public function test_register_validates_required_fields(): void
    {
        $response = $this->postJson('/api/auth/register', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'tenant_id',
                'user_name',
                'user_lastName',
                'user_email',
                'user_password',
                'user_phone_number',
            ]);
    }

    public function test_register_rejects_invalid_email(): void
    {
        $response = $this->postJson('/api/auth/register', $this->validRegisterPayload([
            'user_email' => 'not-an-email',
        ]));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('user_email');
    }

    public function test_register_rejects_short_password(): void
    {
        $response = $this->postJson('/api/auth/register', $this->validRegisterPayload([
            'user_password' => 'short',
            'user_password_confirmation' => 'short',
        ]));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('user_password');
    }

    public function test_register_rejects_unconfirmed_password(): void
    {
        $response = $this->postJson('/api/auth/register', $this->validRegisterPayload([
            'user_password_confirmation' => 'different-password',
        ]));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('user_password');
    }

    public function test_register_rejects_duplicate_email(): void
    {
        $user = $this->createUser();

        $response = $this->postJson('/api/auth/register', $this->validRegisterPayload([
            'user_email' => $user->user_email,
        ]));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('user_email');
    }

    public function test_register_defaults_access_to_owner(): void
    {
        $payload = $this->validRegisterPayload();
        unset($payload['user_access']);

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertCreated()
            ->assertJsonPath('user.user_access', 'owner');
    }

    public function test_register_records_login_history(): void
    {
        $response = $this->postJson('/api/auth/register', $this->validRegisterPayload());

        $response->assertCreated();

        $this->assertDatabaseCount('user_login_history', 1);
        $this->assertDatabaseHas('user_login_history', [
            'user_login_history_user_id' => $response->json('user.id'),
        ]);
    }

    public function test_login_validates_required_fields(): void
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['user_email', 'user_password']);
    }

    public function test_login_with_nonexistent_email_fails(): void
    {
        $this->postJson('/api/auth/login', [
            'user_email' => 'nobody@example.com',
            'user_password' => 'password123',
        ])->assertUnprocessable();
    }

    public function test_login_records_login_history(): void
    {
        $user = $this->createUser();

        $this->postJson('/api/auth/login', [
            'user_email' => $user->user_email,
            'user_password' => 'password123',
        ])->assertOk();

        $this->assertDatabaseCount('user_login_history', 1);
        $this->assertDatabaseHas('user_login_history', [
            'user_login_history_user_id' => $user->id,
        ]);
    }

    public function test_me_requires_authentication(): void
    {
        $this->getJson('/api/auth/me')->assertUnauthorized();
    }

    public function test_logout_requires_authentication(): void
    {
        $this->postJson('/api/auth/logout')->assertUnauthorized();
    }

    public function test_me_returns_correct_user_payload(): void
    {
        $user = $this->createUser();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/auth/me');

        $response->assertOk()
            ->assertJsonStructure([
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
            ])
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.user_name', 'Grace')
            ->assertJsonPath('user.user_lastName', 'Hopper')
            ->assertJsonPath('user.user_access', 'owner');
    }

    public function test_me_response_does_not_expose_password(): void
    {
        $user = $this->createUser();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/auth/me');

        $response->assertOk()
            ->assertJsonMissingPath('user.user_password');
    }

    public function test_logout_invalidates_token(): void
    {
        $user = $this->createUser();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/auth/logout')
            ->assertOk();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }

    public function test_register_returns_token_and_user(): void
    {
        $response = $this->postJson('/api/auth/register', $this->validRegisterPayload());

        $response->assertCreated()
            ->assertJsonStructure(['token', 'user']);

        $this->assertNotEmpty($response->json('token'));
    }

    public function test_login_returns_token_and_user(): void
    {
        $user = $this->createUser();

        $response = $this->postJson('/api/auth/login', [
            'user_email' => $user->user_email,
            'user_password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'user'])
            ->assertJsonPath('user.id', $user->id);

        $this->assertNotEmpty($response->json('token'));
    }
}
