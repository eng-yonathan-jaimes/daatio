<?php

namespace Tests\Unit\Subscriptions;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Subscriptions\app\Models\UserSubscription;
use Tests\TestCase;

class UserSubscriptionModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_table_name_is_user_subscription(): void
    {
        $sub = new UserSubscription();
        $this->assertSame('user_subscription', $sub->getTable());
    }

    public function test_timestamps_are_disabled(): void
    {
        $sub = new UserSubscription();
        $this->assertFalse($sub->timestamps);
    }

    public function test_guarded_is_empty(): void
    {
        $sub = new UserSubscription();
        $this->assertSame([], $sub->getGuarded());
    }

    public function test_casts_correct_attributes(): void
    {
        $sub = new UserSubscription();
        $this->assertSame('decimal:4', $sub->getCasts()['user_subscription_value']);
        $this->assertSame('datetime', $sub->getCasts()['user_subscription_start_date']);
        $this->assertSame('datetime', $sub->getCasts()['user_subscription_end_date']);
        $this->assertSame('datetime', $sub->getCasts()['user_subscription_trial_ends_date']);
        $this->assertSame('datetime', $sub->getCasts()['user_subscription_cancelled_date']);
    }

    public function test_user_relationship(): void
    {
        $sub = new UserSubscription();
        $relation = $sub->user();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('user_subscription_user_id', $relation->getForeignKeyName());
    }

    public function test_subscription_relationship(): void
    {
        $sub = new UserSubscription();
        $relation = $sub->subscription();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('user_subscription_subscription_id', $relation->getForeignKeyName());
    }

    public function test_payments_relationship(): void
    {
        $sub = new UserSubscription();
        $relation = $sub->payments();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame('subscription_payment_user_subscription_id', $relation->getForeignKeyName());
    }

    public function test_is_active(): void
    {
        $sub = new UserSubscription([
            'user_subscription_status' => 'Active',
            'user_subscription_end_date' => now()->addDays(5),
        ]);
        $this->assertTrue($sub->isActive());

        $sub2 = new UserSubscription([
            'user_subscription_status' => 'Expired',
            'user_subscription_end_date' => now()->addDays(5),
        ]);
        $this->assertFalse($sub2->isActive());

        $sub3 = new UserSubscription([
            'user_subscription_status' => 'Active',
            'user_subscription_end_date' => now()->subDays(5),
        ]);
        $this->assertFalse($sub3->isActive());
    }

    public function test_is_expired(): void
    {
        $sub = new UserSubscription([
            'user_subscription_status' => 'Expired',
            'user_subscription_end_date' => now()->addDays(5),
        ]);
        $this->assertTrue($sub->isExpired());

        $sub2 = new UserSubscription([
            'user_subscription_status' => 'Active',
            'user_subscription_end_date' => now()->subDays(5),
        ]);
        $this->assertTrue($sub2->isExpired());

        $sub3 = new UserSubscription([
            'user_subscription_status' => 'Active',
            'user_subscription_end_date' => now()->addDays(5),
        ]);
        $this->assertFalse($sub3->isExpired());
    }

    public function test_days_remaining(): void
    {
        \Illuminate\Support\Carbon::setTestNow('2026-06-09 12:00:00');
        
        $sub = new UserSubscription([
            'user_subscription_end_date' => now()->addDays(10),
        ]);
        $this->assertEquals(10, $sub->daysRemaining());

        $sub2 = new UserSubscription([
            'user_subscription_end_date' => now()->subDays(5),
        ]);
        $this->assertEquals(0, $sub2->daysRemaining());
        
        \Illuminate\Support\Carbon::setTestNow();
    }
}
