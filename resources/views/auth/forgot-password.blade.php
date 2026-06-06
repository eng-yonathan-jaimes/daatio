@extends('auth.layout')

@section('title', 'Forgot Password')

@section('content')
<a href="/daatio/public/" class="back-link">← Back to home</a>

<div class="auth-header">
    <h1>Recover your account</h1>
    <p>Enter your email and we'll send you a reset link</p>
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

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="form-group">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
    </div>

    <button type="submit" class="btn btn-primary">Send reset link</button>
</form>

<div class="auth-divider">or</div>

<div class="auth-footer">
    Remember your password? <a href="{{ route('login') }}">Sign in</a>
</div>
@endsection
