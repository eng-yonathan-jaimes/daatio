@extends('auth.layout')

@section('title', __('messages.reset_password'))

@section('content')
<a href="/daatio/public/" class="back-link">{{ __('messages.back_to_home') }}</a>

<div class="auth-header">
    <h1>{{ __('messages.set_new_password') }}</h1>
    <p>{{ __('messages.set_new_password_desc') }}</p>
</div>

@if($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="form-group">
        <label for="email">{{ __('messages.email_address') }}</label>
        <input type="email" id="email" name="email" value="{{ old('email', $email ?? '') }}" placeholder="you@example.com" required>
    </div>

    <div class="form-group">
        <label for="password">{{ __('messages.new_password') }}</label>
        <input type="password" id="password" name="password" placeholder="{{ __('messages.create_strong_password') }}" required minlength="8">
    </div>

    <div class="form-group">
        <label for="password_confirmation">{{ __('messages.confirm_new_password') }}</label>
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="{{ __('messages.repeat_password') }}" required>
    </div>

    <button type="submit" class="btn btn-primary">{{ __('messages.reset_password') }}</button>
</form>
@endsection
