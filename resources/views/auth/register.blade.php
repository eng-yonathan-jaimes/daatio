@extends('auth.layout')

@section('title', 'Sign Up')

@php
$countryCodes = [
    ['code' => '+1', 'flag' => '🇺🇸', 'name' => 'US/Canada +1'],
    ['code' => '+52', 'flag' => '🇲🇽', 'name' => 'Mexico +52'],
    ['code' => '+503', 'flag' => '🇸🇻', 'name' => 'El Salvador +503'],
    ['code' => '+504', 'flag' => '🇭🇳', 'name' => 'Honduras +504'],
    ['code' => '+505', 'flag' => '🇳🇮', 'name' => 'Nicaragua +505'],
    ['code' => '+506', 'flag' => '🇨🇷', 'name' => 'Costa Rica +506'],
    ['code' => '+507', 'flag' => '🇵🇦', 'name' => 'Panama +507'],
    ['code' => '+57', 'flag' => '🇨🇴', 'name' => 'Colombia +57'],
    ['code' => '+58', 'flag' => '🇻🇪', 'name' => 'Venezuela +58'],
    ['code' => '+51', 'flag' => '🇵🇪', 'name' => 'Peru +51'],
    ['code' => '+593', 'flag' => '🇪🇨', 'name' => 'Ecuador +593'],
    ['code' => '+54', 'flag' => '🇦🇷', 'name' => 'Argentina +54'],
    ['code' => '+56', 'flag' => '🇨🇱', 'name' => 'Chile +56'],
    ['code' => '+55', 'flag' => '🇧🇷', 'name' => 'Brazil +55'],
    ['code' => '+595', 'flag' => '🇵🇾', 'name' => 'Paraguay +595'],
    ['code' => '+598', 'flag' => '🇺🇾', 'name' => 'Uruguay +598'],
    ['code' => '+591', 'flag' => '🇧🇴', 'name' => 'Bolivia +591'],
    ['code' => '+34', 'flag' => '🇪🇸', 'name' => 'Spain +34'],
];
@endphp

@section('content')
<a href="/daatio/public/" class="back-link">← Back to home</a>

<div class="auth-header">
    <a href="/daatio/public/" class="logo">Daatio</a>
    <h1>Create an account</h1>
    <p>Start managing your finances today</p>
</div>

@if($errors->any())
    <div class="alert alert-error">
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div style="display:flex;gap:0.75rem;">
        <div class="form-group" style="flex:1;">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="John" required autofocus>
        </div>
        <div class="form-group" style="flex:1;">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Doe" required>
        </div>
    </div>

    <div class="form-group">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required>
    </div>

    <div class="form-group">
        <label for="phone">Phone number</label>
        <div class="phone-group">
            <select name="country_code" id="country_code" class="country-select">
                @foreach($countryCodes as $c)
                    <option value="{{ $c['code'] }}" {{ old('country_code', '+57') == $c['code'] ? 'selected' : '' }}>{{ $c['flag'] }} {{ $c['code'] }}</option>
                @endforeach
            </select>
            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="318 000 0000" required>
        </div>
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Create a strong password" required minlength="8">
    </div>

    <div class="form-group">
        <label for="password_confirmation">Confirm password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repeat your password" required>
    </div>

    <button type="submit" class="btn btn-primary">Create account</button>
</form>

<div class="auth-divider">or</div>

<div class="auth-footer">
    Already have an account? <a href="{{ route('login') }}">Sign in</a>
</div>
@endsection
