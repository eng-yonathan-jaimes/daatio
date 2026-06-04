<?php

namespace Tests\Unit\Users;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Users\app\Models\User;
use Modules\Users\app\Models\UserLoginHistory;
use Modules\Users\app\Models\UserRecoveryHistory;
use Tests\TestCase;

class ModelRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_login_histories_returns_has_many(): void
    {
        $user = new User;
        $relation = $user->loginHistories();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame('user_login_history_user_id', $relation->getForeignKeyName());
    }

    public function test_user_recovery_histories_returns_has_many(): void
    {
        $user = new User;
        $relation = $user->recoveryHistories();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame('user_recovery_history_user_id', $relation->getForeignKeyName());
    }

    public function test_user_stores_returns_has_many(): void
    {
        $user = new User;
        $relation = $user->stores();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame('store_user_id', $relation->getForeignKeyName());
    }

    public function test_login_history_belongs_to_user(): void
    {
        $history = new UserLoginHistory;
        $relation = $history->user();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('user_login_history_user_id', $relation->getForeignKeyName());
    }

    public function test_recovery_history_belongs_to_user(): void
    {
        $history = new UserRecoveryHistory;
        $relation = $history->user();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('user_recovery_history_user_id', $relation->getForeignKeyName());
    }

    public function test_user_has_many_login_histories_with_data(): void
    {
        $user = User::create([
            'tenant_id' => 1,
            'user_name' => 'Test',
            'user_lastName' => 'User',
            'user_email' => 'rel-test@example.com',
            'user_access' => 'owner',
            'user_password' => 'hashed',
            'user_phone_number' => '+10000000000',
            'user_update_date' => now(),
        ]);

        UserLoginHistory::create([
            'user_login_history_ip' => '127.0.0.1',
            'user_login_history_device' => 'TestDevice',
            'user_login_history_user_id' => $user->id,
        ]);

        UserLoginHistory::create([
            'user_login_history_ip' => '192.168.1.1',
            'user_login_history_device' => 'OtherDevice',
            'user_login_history_user_id' => $user->id,
        ]);

        $this->assertCount(2, $user->loginHistories);
    }

    public function test_login_history_belongs_to_correct_user(): void
    {
        $user = User::create([
            'tenant_id' => 1,
            'user_name' => 'Test',
            'user_lastName' => 'User',
            'user_email' => 'rel-test2@example.com',
            'user_access' => 'owner',
            'user_password' => 'hashed',
            'user_phone_number' => '+10000000000',
            'user_update_date' => now(),
        ]);

        $history = UserLoginHistory::create([
            'user_login_history_ip' => '127.0.0.1',
            'user_login_history_device' => 'TestDevice',
            'user_login_history_user_id' => $user->id,
        ]);

        $this->assertEquals($user->id, $history->user->id);
    }
}
