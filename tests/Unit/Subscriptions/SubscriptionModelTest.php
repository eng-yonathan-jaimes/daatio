<?php

namespace Tests\Unit\Subscriptions;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Subscriptions\app\Models\Subscription;
use Tests\TestCase;

class SubscriptionModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_table_name_is_subscription(): void
    {
        $sub = new Subscription();
        $this->assertSame('subscription', $sub->getTable());
    }

    public function test_timestamps_are_disabled(): void
    {
        $sub = new Subscription();
        $this->assertFalse($sub->timestamps);
    }

    public function test_guarded_is_empty(): void
    {
        $sub = new Subscription();
        $this->assertSame([], $sub->getGuarded());
    }

    public function test_casts_correct_attributes(): void
    {
        $sub = new Subscription();
        $this->assertSame('decimal:4', $sub->getCasts()['subscription_value']);
        $this->assertSame('boolean', $sub->getCasts()['subscription_enabled']);
        $this->assertSame('datetime', $sub->getCasts()['subscription_creation_date']);
    }

    public function test_user_subscriptions_relationship(): void
    {
        $sub = new Subscription();
        $relation = $sub->userSubscriptions();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame('user_subscription_subscription_id', $relation->getForeignKeyName());
    }
}
