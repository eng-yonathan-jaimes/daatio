<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Subscriptions\app\Models\Subscription;
use Modules\Subscriptions\app\Models\SubscriptionPayment;
use Modules\Subscriptions\app\Models\UserSubscription;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $activeSubscription = UserSubscription::where('user_subscription_user_id', $user->id)
            ->with('subscription', 'payments')
            ->latest('user_subscription_start_date')
            ->first();

        $plans = Subscription::where('subscription_enabled', true)
            ->where('subscription_type', '!=', 'Trial')
            ->get();

        return view('subscription.index', compact('activeSubscription', 'plans'));
    }

    public function renew(Request $request)
    {
        $user = Auth::user();

        $plan = Subscription::where('subscription_enabled', true)
            ->where('subscription_type', '!=', 'Trial')
            ->findOrFail($request->input('plan_id'));

        $activeSubscription = UserSubscription::where('user_subscription_user_id', $user->id)
            ->where('user_subscription_status', 'Active')
            ->first();

        if ($activeSubscription) {
            $activeSubscription->update([
                'user_subscription_status' => 'Cancelled',
                'user_subscription_cancelled_date' => now(),
            ]);
        }

        $newSubscription = UserSubscription::create([
            'user_subscription_user_id' => $user->id,
            'user_subscription_subscription_id' => $plan->id,
            'user_subscription_start_date' => now(),
            'user_subscription_end_date' => now()->addDays($plan->subscription_days),
            'user_subscription_value' => $plan->subscription_value,
            'user_subscription_status' => 'Active',
        ]);

        SubscriptionPayment::create([
            'subscription_payment_user_subscription_id' => $newSubscription->id,
            'subscription_payment_amount' => $plan->subscription_value,
            'subscription_payment_date' => now(),
            'subscription_payment_method' => 'Manual',
            'subscription_payment_status' => 'Completed',
            'subscription_payment_reference' => 'DEV-' . now()->format('YmdHis'),
            'subscription_payment_period_start' => now(),
            'subscription_payment_period_end' => now()->addDays($plan->subscription_days),
        ]);

        return redirect()->route('subscription.index')
            ->with('status', 'Subscription activated! Your plan is now ' . $plan->subscription_type . '.');
    }
}
