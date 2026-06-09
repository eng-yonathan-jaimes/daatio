<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Modules\Subscriptions\app\Models\Subscription;
use Modules\Subscriptions\app\Models\UserSubscription;
use Modules\Stores\app\Models\Store;
use Modules\Users\app\Models\User;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:user,user_email'],
            'country_code' => ['required', 'string', 'max:5'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $fullPhone = $validated['country_code'] . ' ' . $validated['phone'];

        $existingPhone = User::where('user_phone_number', $fullPhone)->exists();
        if ($existingPhone) {
            return back()->withErrors(['phone' => 'This phone number is already registered.'])->withInput();
        }

        $now = now();

        $user = User::create([
            'tenant_id' => 1,
            'user_name' => $validated['first_name'],
            'user_lastName' => $validated['last_name'],
            'user_email' => $validated['email'],
            'user_phone_number' => $fullPhone,
            'user_password' => Hash::make($validated['password']),
            'user_access' => 'user',
            'user_creation' => $now,
            'user_update_date' => $now,
        ]);

        $user->generateEmailVerificationCode();
        $user->generatePhoneVerificationCode();

        $trialPlan = Subscription::where('subscription_type', 'Trial')
            ->where('subscription_enabled', true)
            ->first();

        if ($trialPlan) {
            UserSubscription::create([
                'user_subscription_user_id' => $user->id,
                'user_subscription_subscription_id' => $trialPlan->id,
                'user_subscription_start_date' => $now,
                'user_subscription_end_date' => $now->copy()->addDays($trialPlan->subscription_days),
                'user_subscription_value' => $trialPlan->subscription_value,
                'user_subscription_status' => 'Active',
                'user_subscription_trial_ends_date' => $now->copy()->addDays($trialPlan->subscription_days),
            ]);
        }

        Store::create([
            'store_user_id' => $user->id,
            'store_name' => $validated['first_name'] . "'s Store",
            'store_active' => true,
            'store_update_date' => $now,
            'store_address' => '',
            'store_type_id' => 1,
            'store_location' => 'Physical',
            'store_registration_date' => $now,
        ]);

        auth()->login($user);
        $request->session()->regenerate();

        return redirect()->route('verification.notice');
    }
}
