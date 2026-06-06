@extends('auth.layout')

@section('title', 'Reset Password')

@section('content')
<a href="/daatio/public/" class="back-link">← Back to home</a>

<div class="auth-header">
    <h1>Set new password</h1>
    <p>Create a new password for your account</p>
</div>

@if($errors->any())
    <div class="alert alert-error">
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="form-group">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" value="{{ old('email', $email ?? '') }}" placeholder="you@example.com" required>
    </div>

    <div class="form-group">
        <label for="password">New password</label>
        <input type="password" id="password" name="password" placeholder="Create a strong password" required minlength="8">
    </div>

    <div class="form-group">
        <label for="password_confirmation">Confirm new password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repeat your password" required>
    </div>

    <button type="submit" class="btn btn-primary">Reset password</button>
</form>
@endsection
