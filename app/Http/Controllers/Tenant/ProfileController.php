<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('tenant.profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'user_name' => ['required', 'string', 'max:255'],
            'user_lastName' => ['required', 'string', 'max:255'],
            'user_email' => ['required', 'email', 'max:255', "unique:user,user_email,{$user->id}"],
            'user_phone_number' => ['required', 'string', 'max:255'],
        ]);

        $user->update($validated);

        return back()->with('status', __('messages.profile_updated'));
    }
}
