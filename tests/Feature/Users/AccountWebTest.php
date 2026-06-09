<?php

namespace Tests\Feature\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Modules\Users\app\Models\User;
use Modules\Users\app\Models\UserLoginHistory;
use Modules\Users\app\Models\UserRecoveryHistory;
use Tests\TestCase;

class AccountWebTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'tenant_id' => 1,
            'user_name' => 'John',
            'user_lastName' => 'Doe',
            'user_email' => 'john.doe@example.com',
            'user_access' => 'owner',
            'user_password' => bcrypt('password123'),
            'user_phone_number' => '+123456789',
            'user_update_date' => now(),
        ], $overrides));
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('account.profile.edit'))->assertRedirect(route('login'));
        $this->post(route('account.profile.update'), [])->assertRedirect(route('login'));
        $this->get(route('account.security.index'))->assertRedirect(route('login'));
        $this->post(route('account.security.password'), [])->assertRedirect(route('login'));
        $this->get(route('account.subscription.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_profile_edit_form(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get(route('account.profile.edit'));

        $response->assertOk()
            ->assertViewIs('tenant.profile')
            ->assertViewHas('user')
            ->assertSee('John')
            ->assertSee('Doe')
            ->assertSee('john.doe@example.com');
    }

    public function test_profile_update_validation(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post(route('account.profile.update'), []);

        $response->assertSessionHasErrors(['user_name', 'user_lastName', 'user_email', 'user_phone_number']);
    }

    public function test_authenticated_user_can_update_profile(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post(route('account.profile.update'), [
            'user_name' => 'Jane',
            'user_lastName' => 'Smith',
            'user_email' => 'jane.smith@example.com',
            'user_phone_number' => '+987654321',
        ]);

        $response->assertRedirect()
            ->assertSessionHas('status', __('messages.profile_updated'));

        $this->assertDatabaseHas('user', [
            'id' => $user->id,
            'user_name' => 'Jane',
            'user_lastName' => 'Smith',
            'user_email' => 'jane.smith@example.com',
            'user_phone_number' => '+987654321',
        ]);
    }

    public function test_email_uniqueness_on_profile_update(): void
    {
        $user1 = $this->createUser(['user_email' => 'user1@example.com']);
        $user2 = $this->createUser(['user_email' => 'user2@example.com']);

        // Try updating user1's email to user2's email
        $response = $this->actingAs($user1)->post(route('account.profile.update'), [
            'user_name' => 'John',
            'user_lastName' => 'Doe',
            'user_email' => 'user2@example.com',
            'user_phone_number' => '+123456789',
        ]);

        $response->assertSessionHasErrors(['user_email']);
    }

    public function test_authenticated_user_can_view_security_logs(): void
    {
        $user = $this->createUser();

        UserLoginHistory::create([
            'user_login_history_user_id' => $user->id,
            'user_login_history_ip' => '127.0.0.1',
            'user_login_history_device' => 'Chrome on Windows',
            'user_login_history_login_date' => now(),
        ]);

        UserRecoveryHistory::create([
            'user_recovery_history_user_id' => $user->id,
            'user_recovery_history_intent_date' => now(),
            'user_recovery_history_recovery_answered' => 'yes',
            'user_recovery_history_recovered_success' => true,
            'user_recovery_history_method_used' => 'email',
            'user_recovery_history_ip' => '127.0.0.1',
            'user_recovery_history_decive' => 'Firefox on Linux',
        ]);

        $response = $this->actingAs($user)->get(route('account.security.index'));

        $response->assertOk()
            ->assertViewIs('tenant.security')
            ->assertViewHas('loginHistory')
            ->assertViewHas('recoveryHistory')
            ->assertSee('Chrome on Windows');

        $this->assertDatabaseHas('user_recovery_history', [
            'user_recovery_history_user_id' => $user->id,
            'user_recovery_history_decive' => 'Firefox on Linux',
        ]);
    }

    public function test_password_update_validation(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post(route('account.security.password'), []);

        $response->assertSessionHasErrors(['current_password', 'new_password']);
    }

    public function test_password_update_fails_with_invalid_current_password(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post(route('account.security.password'), [
            'current_password' => 'wrong_password',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors(['current_password']);
        $this->assertTrue(Hash::check('password123', $user->fresh()->user_password));
    }

    public function test_authenticated_user_can_update_password(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post(route('account.security.password'), [
            'current_password' => 'password123',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect()
            ->assertSessionHas('status', __('messages.password_updated'));

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->user_password));
    }

    public function test_authenticated_user_can_view_account_subscription_view(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get(route('account.subscription.index'));

        $response->assertOk()
            ->assertViewIs('tenant.subscription');
    }
}
