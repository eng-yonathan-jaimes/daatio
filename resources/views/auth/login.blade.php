@extends('auth.layout')

@section('title', __('messages.login'))

@section('content')
<a href="/daatio/public/" class="back-link">{{ __('messages.back_to_home') }}</a>

<div class="auth-header">
    <a href="/daatio/public/" class="logo">Daatio</a>
    <h1>{{ __('messages.welcome_back') }}</h1>
    <p>{{ __('messages.enter_credentials') }}</p>
</div>

@if($errors->any())
    <div class="alert alert-error">
        {{ $errors->first() }}
    </div>
@endif

@if(session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="form-group">
        <label for="email">{{ __('messages.email_address') }}</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
    </div>

    <div class="form-group">
        <label for="password">{{ __('messages.password') }}</label>
        <input type="password" id="password" name="password" placeholder="{{ __('messages.create_strong_password') }}" required>
    </div>

    <div class="checkbox-group" style="margin-bottom: 1.5rem;">
        <input type="checkbox" id="remember" name="remember">
        <label for="remember">{{ __('messages.remember_me') }}</label>
        <a href="{{ route('password.request') }}" class="btn-link" style="margin-left: auto;">{{ __('messages.forgot_password') }}</a>
    </div>

    <button type="submit" class="btn btn-primary">{{ __('messages.sign_in') }}</button>
</form>

<div class="auth-divider">{{ __('messages.or') }}</div>

<div class="auth-footer">
    {{ __('messages.dont_have_account') }} <a href="{{ route('register') }}">{{ __('messages.sign_up') }}</a>
</div>
@endsection
