<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Users\app\Models\UserLoginHistory;
use Modules\Users\app\Models\UserRecoveryHistory;

class SecurityController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $loginHistory = UserLoginHistory::where('user_login_history_user_id', $user->id)
            ->orderBy('user_login_history_login_date', 'desc')
            ->limit(50)
            ->get();

        $recoveryHistory = UserRecoveryHistory::where('user_recovery_history_user_id', $user->id)
            ->orderBy('user_recovery_history_intent_date', 'desc')
            ->limit(50)
            ->get();

        return view('tenant.security', compact('loginHistory', 'recoveryHistory'));
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($validated['current_password'], $user->getAuthPassword())) {
            return back()->withErrors(['current_password' => __('messages.invalid_current_password')]);
        }

        $user->update([
            'user_password' => Hash::make($validated['new_password']),
            'user_update_date' => now(),
        ]);

        return back()->with('status', __('messages.password_updated'));
    }
}
