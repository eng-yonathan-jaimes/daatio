<?php

namespace Tests\Feature\Subscriptions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Subscriptions\app\Models\Subscription;
use Modules\Subscriptions\app\Models\UserSubscription;
use Modules\Users\app\Models\User;
use Tests\TestCase;

class SubscriptionWebTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::create([
            'tenant_id' => 1,
            'user_name' => 'John',
            'user_lastName' => 'Doe',
            'user_email' => 'john.doe@example.com',
            'user_access' => 'owner',
            'user_password' => bcrypt('password123'),
            'user_phone_number' => '+123456789',
            'user_update_date' => now(),
        ]);
    }

    private function createPlans(): array
    {
        $monthly = Subscription::create([
            'subscription_type' => 'Monthly Plan',
            'subscription_description' => '30 days plan',
            'subscription_value' => 29.99,
            'subscription_period' => 'Monthly',
            'subscription_days' => 30,
            'subscription_max_stores' => 1,
            'subscription_creation_date' => now(),
            'subscription_enabled' => true,
        ]);

        $annual = Subscription::create([
            'subscription_type' => 'Annual Plan',
            'subscription_description' => '365 days plan',
            'subscription_value' => 299.99,
            'subscription_period' => 'Annual',
            'subscription_days' => 365,
            'subscription_max_stores' => 3,
            'subscription_creation_date' => now(),
            'subscription_enabled' => true,
        ]);

        return [$monthly, $annual];
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('subscription.index'))->assertRedirect(route('login'));
        $this->post(route('subscription.renew'), [])->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_plans_index(): void
    {
        $user = $this->createUser();
        [$monthly, $annual] = $this->createPlans();

        $response = $this->actingAs($user)->get(route('subscription.index'));

        $response->assertOk()
            ->assertViewIs('subscription.index')
            ->assertViewHas('activeSubscription')
            ->assertViewHas('plans')
            ->assertSee('Monthly Plan')
            ->assertSee('Annual Plan');
    }

    public function test_authenticated_user_can_purchase_and_renew_plan(): void
    {
        $user = $this->createUser();
        [$monthly, $annual] = $this->createPlans();

        // 1. Initial purchase (no active subscription)
        $response = $this->actingAs($user)->post(route('subscription.renew'), [
            'plan_id' => $monthly->id,
        ]);

        $response->assertRedirect(route('subscription.index'))
            ->assertSessionHas('status', 'Subscription activated! Your plan is now Monthly Plan.');

        $this->assertDatabaseHas('user_subscription', [
            'user_subscription_user_id' => $user->id,
            'user_subscription_subscription_id' => $monthly->id,
            'user_subscription_status' => 'Active',
            'user_subscription_value' => 29.99,
        ]);

        $newSubscription = UserSubscription::where('user_subscription_user_id', $user->id)
            ->where('user_subscription_status', 'Active')
            ->first();

        $this->assertDatabaseHas('subscription_payment', [
            'subscription_payment_user_subscription_id' => $newSubscription->id,
            'subscription_payment_amount' => 29.99,
            'subscription_payment_method' => 'Manual',
            'subscription_payment_status' => 'Completed',
        ]);

        // 2. Renewal / Upgrade to Annual (which cancels the Monthly subscription)
        $response = $this->actingAs($user)->post(route('subscription.renew'), [
            'plan_id' => $annual->id,
        ]);

        $response->assertRedirect(route('subscription.index'))
            ->assertSessionHas('status', 'Subscription activated! Your plan is now Annual Plan.');

        // Monthly plan is now Cancelled
        $newSubscription->refresh();
        $this->assertEquals('Cancelled', $newSubscription->user_subscription_status);
        $this->assertNotNull($newSubscription->user_subscription_cancelled_date);

        // Annual plan is now Active
        $this->assertDatabaseHas('user_subscription', [
            'user_subscription_user_id' => $user->id,
            'user_subscription_subscription_id' => $annual->id,
            'user_subscription_status' => 'Active',
            'user_subscription_value' => 299.99,
        ]);
    }
}
