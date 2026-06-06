<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Modules\Users\app\Models\User;
use Modules\Users\app\Models\UserRecoveryHistory;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('user_email', $request->email)->first();

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($user) {
            UserRecoveryHistory::create([
                'user_recovery_history_intent_date' => now(),
                'user_recovery_history_recovery_answered' => 'no',
                'user_recovery_history_recovered_success' => $status === Password::RESET_LINK_SENT,
                'user_recovery_history_method_used' => 'email',
                'user_recovery_history_ip' => $request->ip(),
                'user_recovery_history_user_id' => $user->id,
                'user_recovery_history_decive' => $request->userAgent() ?? 'unknown',
            ]);
        }

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }
}
