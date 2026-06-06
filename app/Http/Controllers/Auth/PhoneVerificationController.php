<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhoneVerificationController extends Controller
{
    public function show()
    {
        if (!Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }
        if (Auth::user()->hasVerifiedPhone()) {
            return redirect()->intended('/dashboard');
        }
        return view('auth.verify-phone')->with('phone', Auth::user()->phone);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();

        if ($user->phone_verification_code === $request->code) {
            $user->markPhoneAsVerified();
            $user->phone_verification_code = null;
            $user->save();

            return redirect()->intended('/dashboard')->with('status', 'Phone verified successfully!');
        }

        return back()->withErrors(['code' => 'Invalid verification code.']);
    }

    public function resend(Request $request)
    {
        $user = Auth::user();

        if (!Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }
        if ($user->hasVerifiedPhone()) {
            return redirect()->intended('/dashboard');
        }

        $user->generatePhoneVerificationCode();
        $user->sendPhoneVerificationSms();

        return back()->with('status', 'Verification code sent!');
    }
}
