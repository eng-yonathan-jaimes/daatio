<?php

namespace Tests\Unit\Subscriptions;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Subscriptions\app\Models\SubscriptionPayment;
use Tests\TestCase;

class SubscriptionPaymentModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_table_name_is_subscription_payment(): void
    {
        $payment = new SubscriptionPayment();
        $this->assertSame('subscription_payment', $payment->getTable());
    }

    public function test_timestamps_are_disabled(): void
    {
        $payment = new SubscriptionPayment();
        $this->assertFalse($payment->timestamps);
    }

    public function test_guarded_is_empty(): void
    {
        $payment = new SubscriptionPayment();
        $this->assertSame([], $payment->getGuarded());
    }

    public function test_casts_correct_attributes(): void
    {
        $payment = new SubscriptionPayment();
        $this->assertSame('decimal:4', $payment->getCasts()['subscription_payment_amount']);
        $this->assertSame('datetime', $payment->getCasts()['subscription_payment_date']);
        $this->assertSame('datetime', $payment->getCasts()['subscription_payment_period_start']);
        $this->assertSame('datetime', $payment->getCasts()['subscription_payment_period_end']);
    }

    public function test_user_subscription_relationship(): void
    {
        $payment = new SubscriptionPayment();
        $relation = $payment->userSubscription();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('subscription_payment_user_subscription_id', $relation->getForeignKeyName());
    }
}
