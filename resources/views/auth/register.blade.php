@extends('auth.layout')

@section('title', __('messages.sign_up'))

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
<a href="/daatio/public/" class="back-link">{{ __('messages.back_to_home') }}</a>

<div class="auth-header">
    <a href="/daatio/public/" class="logo">Daatio</a>
    <h1>{{ __('messages.create_account') }}</h1>
    <p>{{ __('messages.start_managing') }}</p>
</div>

@if($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div style="display:flex;gap:0.75rem;">
        <div class="form-group" style="flex:1;">
            <label for="first_name">{{ __('messages.first_name') }}</label>
            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="John" required autofocus>
        </div>
        <div class="form-group" style="flex:1;">
            <label for="last_name">{{ __('messages.last_name') }}</label>
            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Doe" required>
        </div>
    </div>

    <div class="form-group">
        <label for="email">{{ __('messages.email_address') }}</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required>
    </div>

    <div class="form-group">
        <label for="phone">{{ __('messages.phone_number') }}</label>
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
        <label for="password">{{ __('messages.password') }}</label>
        <input type="password" id="password" name="password" placeholder="{{ __('messages.create_strong_password') }}" required minlength="8">
    </div>

    <div class="form-group">
        <label for="password_confirmation">{{ __('messages.confirm_password') }}</label>
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="{{ __('messages.repeat_password') }}" required>
    </div>

    <button type="submit" class="btn btn-primary">{{ __('messages.create_account') }}</button>
</form>

<div class="auth-divider">{{ __('messages.or') }}</div>

<div class="auth-footer">
    {{ __('messages.already_have_account') }} <a href="{{ route('login') }}">{{ __('messages.sign_in') }}</a>
</div>
@endsection
