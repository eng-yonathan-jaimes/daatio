<?php

namespace Tests\Unit\Users;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\Users\app\Models\User;
use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = new User;
    }

    public function test_extends_authenticatable(): void
    {
        $this->assertInstanceOf(Authenticatable::class, $this->user);
    }

    public function test_table_name_is_user(): void
    {
        $this->assertSame('user', $this->user->getTable());
    }

    public function test_timestamps_are_disabled(): void
    {
        $this->assertFalse($this->user->timestamps);
    }

    public function test_password_is_hidden(): void
    {
        $this->assertContains('user_password', $this->user->getHidden());
    }

    public function test_remember_token_is_hidden(): void
    {
        $this->assertContains('remember_token', $this->user->getHidden());
    }

    public function test_guarded_is_empty_array(): void
    {
        $this->assertSame([], $this->user->getGuarded());
    }

    public function test_casts_user_creation_to_datetime(): void
    {
        $casts = $this->user->getCasts();
        $this->assertArrayHasKey('user_creation', $casts);
        $this->assertSame('datetime', $casts['user_creation']);
    }

    public function test_casts_user_update_date_to_datetime(): void
    {
        $casts = $this->user->getCasts();
        $this->assertArrayHasKey('user_update_date', $casts);
        $this->assertSame('datetime', $casts['user_update_date']);
    }

    public function test_get_auth_password_returns_user_password(): void
    {
        $this->user->user_password = 'hashed-secret';

        $this->assertSame('hashed-secret', $this->user->getAuthPassword());
    }

    public function test_has_login_histories_method(): void
    {
        $this->assertTrue(method_exists($this->user, 'loginHistories'));
    }

    public function test_has_recovery_histories_method(): void
    {
        $this->assertTrue(method_exists($this->user, 'recoveryHistories'));
    }

    public function test_has_stores_method(): void
    {
        $this->assertTrue(method_exists($this->user, 'stores'));
    }
}
