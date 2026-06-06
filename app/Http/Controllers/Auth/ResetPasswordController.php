<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Modules\Users\app\Models\User;
use Modules\Users\app\Models\UserRecoveryHistory;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'user_password' => Hash::make($password),
                ])->save();
            }
        );

        $user = User::where('user_email', $request->email)->first();

        if ($user) {
            UserRecoveryHistory::create([
                'user_recovery_history_intent_date' => now(),
                'user_recovery_history_recovery_answered' => $status === Password::PASSWORD_RESET ? 'yes' : 'no',
                'user_recovery_history_recovered_success' => $status === Password::PASSWORD_RESET,
                'user_recovery_history_method_used' => 'email',
                'user_recovery_history_ip' => $request->ip(),
                'user_recovery_history_user_id' => $user->id,
                'user_recovery_history_decive' => $request->userAgent() ?? 'unknown',
            ]);
        }

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
