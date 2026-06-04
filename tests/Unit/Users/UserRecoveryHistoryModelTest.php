<?php

namespace Tests\Unit\Users;

use Modules\Users\app\Models\UserRecoveryHistory;
use PHPUnit\Framework\TestCase;

class UserRecoveryHistoryModelTest extends TestCase
{
    private UserRecoveryHistory $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new UserRecoveryHistory;
    }

    public function test_table_name_is_user_recovery_history(): void
    {
        $this->assertSame('user_recovery_history', $this->model->getTable());
    }

    public function test_timestamps_are_disabled(): void
    {
        $this->assertFalse($this->model->timestamps);
    }

    public function test_guarded_is_empty_array(): void
    {
        $this->assertSame([], $this->model->getGuarded());
    }

    public function test_casts_intent_date_to_datetime(): void
    {
        $casts = $this->model->getCasts();
        $this->assertArrayHasKey('user_recovery_history_intent_date', $casts);
        $this->assertSame('datetime', $casts['user_recovery_history_intent_date']);
    }

    public function test_casts_recovered_success_to_boolean(): void
    {
        $casts = $this->model->getCasts();
        $this->assertArrayHasKey('user_recovery_history_recovered_success', $casts);
        $this->assertSame('boolean', $casts['user_recovery_history_recovered_success']);
    }

    public function test_has_user_relationship_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'user'));
    }
}
