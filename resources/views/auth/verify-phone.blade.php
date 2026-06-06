@extends('auth.layout')

@section('title', 'Verify Phone')

@section('content')
<a href="/daatio/public/" class="back-link">← Back to home</a>

<div class="auth-header">
    <h1>Verify your phone</h1>
    <p>Enter the code sent to <strong>{{ $phone }}</strong></p>
</div>

@if($errors->any())
    <div class="alert alert-error">
        {{ $errors->first() }}
    </div>
@endif

@php $code = auth()->user()->phone_verification_code; @endphp
@if($code)
    <div class="alert alert-info" style="text-align:center;">
        <div style="font-size:0.75rem;margin-bottom:0.25rem;">DEV — your code is:</div>
        <div style="font-family:var(--font-mono);font-size:2rem;font-weight:700;letter-spacing:0.3em;">{{ $code }}</div>
    </div>
@endif

<form method="POST" action="{{ route('phone.verification.verify') }}">
    @csrf
    <div class="form-group">
        <label for="code">Verification code</label>
        <input type="text" id="code" name="code" class="code-input" placeholder="000000" maxlength="6" required autofocus>
    </div>

    <button type="submit" class="btn btn-primary">Verify phone</button>
</form>

<form method="POST" action="{{ route('phone.verification.resend') }}" style="margin-top: 1rem;">
    @csrf
    <div class="resend-link">
        Didn't receive a code? <button type="submit">Resend</button>
    </div>
</form>
@endsection
