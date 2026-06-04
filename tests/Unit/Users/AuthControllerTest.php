<?php

namespace Tests\Unit\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Modules\Users\app\Http\Controllers\Api\AuthController;
use Modules\Users\app\Models\User;
use Modules\Users\app\Models\UserLoginHistory;
use ReflectionMethod;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    private AuthController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new AuthController;
    }

    private function invokePrivate(string $method, array $args = []): mixed
    {
        $reflected = new ReflectionMethod(AuthController::class, $method);

        return $reflected->invokeArgs($this->controller, $args);
    }

    public function test_token_name_returns_user_agent(): void
    {
        $request = Request::create('/api/auth/login', 'POST', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 TestBrowser',
        ]);

        $result = $this->invokePrivate('tokenName', [$request]);

        $this->assertSame('Mozilla/5.0 TestBrowser', $result);
    }

    public function test_token_name_defaults_to_api_token_when_no_user_agent(): void
    {
        $request = Request::create('/api/auth/login', 'POST');
        $request->headers->remove('User-Agent');

        $result = $this->invokePrivate('tokenName', [$request]);

        $this->assertSame('api-token', $result);
    }

    public function test_token_name_is_truncated_to_255_characters(): void
    {
        $longAgent = str_repeat('A', 300);
        $request = Request::create('/api/auth/login', 'POST', [], [], [], [
            'HTTP_USER_AGENT' => $longAgent,
        ]);

        $result = $this->invokePrivate('tokenName', [$request]);

        $this->assertSame(255, strlen($result));
    }

    public function test_user_payload_returns_expected_keys(): void
    {
        $user = User::create([
            'tenant_id' => 1,
            'user_name' => 'Test',
            'user_lastName' => 'User',
            'user_email' => 'test@example.com',
            'user_access' => 'owner',
            'user_password' => 'hashed',
            'user_phone_number' => '+10000000000',
            'user_update_date' => now(),
        ]);

        $result = $this->invokePrivate('userPayload', [$user]);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('tenant_id', $result);
        $this->assertArrayHasKey('user_name', $result);
        $this->assertArrayHasKey('user_lastName', $result);
        $this->assertArrayHasKey('user_email', $result);
        $this->assertArrayHasKey('user_access', $result);
        $this->assertArrayHasKey('user_phone_number', $result);
        $this->assertArrayHasKey('user_creation', $result);
        $this->assertArrayHasKey('user_update_date', $result);
    }

    public function test_user_payload_does_not_include_password(): void
    {
        $user = User::create([
            'tenant_id' => 1,
            'user_name' => 'Test',
            'user_lastName' => 'User',
            'user_email' => 'test@example.com',
            'user_access' => 'owner',
            'user_password' => 'hashed',
            'user_phone_number' => '+10000000000',
            'user_update_date' => now(),
        ]);

        $result = $this->invokePrivate('userPayload', [$user]);

        $this->assertArrayNotHasKey('user_password', $result);
    }

    public function test_user_payload_returns_correct_values(): void
    {
        $user = User::create([
            'tenant_id' => 5,
            'user_name' => 'John',
            'user_lastName' => 'Doe',
            'user_email' => 'john@example.com',
            'user_access' => 'admin',
            'user_password' => 'hashed',
            'user_phone_number' => '+1234567890',
            'user_update_date' => now(),
        ]);

        $result = $this->invokePrivate('userPayload', [$user]);

        $this->assertSame($user->id, $result['id']);
        $this->assertSame(5, $result['tenant_id']);
        $this->assertSame('John', $result['user_name']);
        $this->assertSame('Doe', $result['user_lastName']);
        $this->assertSame('john@example.com', $result['user_email']);
        $this->assertSame('admin', $result['user_access']);
        $this->assertSame('+1234567890', $result['user_phone_number']);
    }

    public function test_record_login_creates_login_history(): void
    {
        $user = User::create([
            'tenant_id' => 1,
            'user_name' => 'Test',
            'user_lastName' => 'User',
            'user_email' => 'test@example.com',
            'user_access' => 'owner',
            'user_password' => 'hashed',
            'user_phone_number' => '+10000000000',
            'user_update_date' => now(),
        ]);

        $request = Request::create('/api/auth/login', 'POST', [], [], [], [
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_USER_AGENT' => 'TestAgent',
        ]);

        $this->invokePrivate('recordLogin', [$request, $user]);

        $this->assertDatabaseCount('user_login_history', 1);
        $this->assertDatabaseHas('user_login_history', [
            'user_login_history_user_id' => $user->id,
            'user_login_history_ip' => '192.168.1.1',
            'user_login_history_device' => 'TestAgent',
        ]);
    }

    public function test_record_login_truncates_ip_to_15_characters(): void
    {
        $user = User::create([
            'tenant_id' => 1,
            'user_name' => 'Test',
            'user_lastName' => 'User',
            'user_email' => 'test@example.com',
            'user_access' => 'owner',
            'user_password' => 'hashed',
            'user_phone_number' => '+10000000000',
            'user_update_date' => now(),
        ]);

        $request = Request::create('/api/auth/login', 'POST', [], [], [], [
            'REMOTE_ADDR' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
            'HTTP_USER_AGENT' => 'TestAgent',
        ]);

        $this->invokePrivate('recordLogin', [$request, $user]);

        $history = UserLoginHistory::first();
        $this->assertSame(15, strlen($history->user_login_history_ip));
    }

    public function test_record_login_truncates_device_to_255_characters(): void
    {
        $user = User::create([
            'tenant_id' => 1,
            'user_name' => 'Test',
            'user_lastName' => 'User',
            'user_email' => 'test@example.com',
            'user_access' => 'owner',
            'user_password' => 'hashed',
            'user_phone_number' => '+10000000000',
            'user_update_date' => now(),
        ]);

        $longAgent = str_repeat('B', 300);
        $request = Request::create('/api/auth/login', 'POST', [], [], [], [
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_USER_AGENT' => $longAgent,
        ]);

        $this->invokePrivate('recordLogin', [$request, $user]);

        $history = UserLoginHistory::first();
        $this->assertSame(255, strlen($history->user_login_history_device));
    }
}
