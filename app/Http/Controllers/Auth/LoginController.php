<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Users\app\Models\UserLoginHistory;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $authCredentials = [
            'user_email' => $credentials['email'],
            'password' => $credentials['password'],
        ];

        if (Auth::attempt($authCredentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            UserLoginHistory::create([
                'user_login_history_login_date' => now(),
                'user_login_history_ip' => $request->ip(),
                'user_login_history_device' => $request->userAgent() ?? 'unknown',
                'user_login_history_user_id' => Auth::id(),
            ]);

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
