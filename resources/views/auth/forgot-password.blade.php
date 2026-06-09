@extends('auth.layout')

@section('title', __('messages.reset_password'))

@section('content')
<a href="/daatio/public/" class="back-link">{{ __('messages.back_to_home') }}</a>

<div class="auth-header">
    <h1>{{ __('messages.recover_account') }}</h1>
    <p>{{ __('messages.recover_account_desc') }}</p>
</div>

@if($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
@endif

@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="form-group">
        <label for="email">{{ __('messages.email_address') }}</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
    </div>
    <button type="submit" class="btn btn-primary">{{ __('messages.send_reset_link') }}</button>
</form>

<div class="auth-divider">{{ __('messages.or') }}</div>

<div class="auth-footer">
    {{ __('messages.remember_password') }} <a href="{{ route('login') }}">{{ __('messages.sign_in') }}</a>
</div>
@endsection
