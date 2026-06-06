@extends('auth.layout')

@section('title', 'Login')

@section('content')
<a href="/daatio/public/" class="back-link">← Back to home</a>

<div class="auth-header">
    <a href="/daatio/public/" class="logo">Daatio</a>
    <h1>Welcome back</h1>
    <p>Enter your credentials to access your account</p>
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
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>
    </div>

    <div class="checkbox-group" style="margin-bottom: 1.5rem;">
        <input type="checkbox" id="remember" name="remember">
        <label for="remember">Remember me</label>
        <a href="{{ route('password.request') }}" class="btn-link" style="margin-left: auto;">Forgot password?</a>
    </div>

    <button type="submit" class="btn btn-primary">Sign in</button>
</form>

<div class="auth-divider">or</div>

<div class="auth-footer">
    Don't have an account? <a href="{{ route('register') }}">Sign up</a>
</div>
@endsection
