<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailVerificationController extends Controller
{
    public function show()
    {
        if (Auth::user()->hasVerifiedEmail()) {
            if (!Auth::user()->hasVerifiedPhone()) {
                return redirect()->route('phone.verification.notice');
            }
            return redirect()->intended('/dashboard');
        }
        return view('auth.verify-email')->with('email', Auth::user()->email);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();

        if ($user->email_verification_code === $request->code) {
            $user->markEmailAsVerified();
            $user->email_verification_code = null;
            $user->save();

            return redirect()->route('phone.verification.notice')->with('status', 'Email verified! Now verify your phone.');
        }

        return back()->withErrors(['code' => 'Invalid verification code.']);
    }

    public function resend(Request $request)
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            if (!$user->hasVerifiedPhone()) {
                return redirect()->route('phone.verification.notice');
            }
            return redirect()->intended('/dashboard');
        }

        $user->generateEmailVerificationCode();
        $user->sendEmailVerificationNotification();

        return back()->with('status', 'Verification code sent!');
    }
}
