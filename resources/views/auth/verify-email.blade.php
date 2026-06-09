@extends('auth.layout')

@section('title', __('messages.verify_email'))

@section('content')
<a href="/daatio/public/" class="back-link">{{ __('messages.back_to_home') }}</a>

<div class="auth-header">
    <h1>{{ __('messages.verify_email') }}</h1>
    <p>{{ __('messages.verify_email_desc', ['email' => $email]) }}</p>
</div>

@if($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
@endif

@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

@php $code = auth()->user()->email_verification_code; @endphp
@if(config('app.debug') && $code)
    <div class="alert alert-info" style="text-align:center;">
        <div style="font-size:0.75rem;margin-bottom:0.25rem;">{{ __('messages.dev_code_label') }}</div>
        <div style="font-family:var(--font-mono);font-size:2rem;font-weight:700;letter-spacing:0.3em;">{{ $code }}</div>
    </div>
@endif

<form method="POST" action="{{ route('verification.verify') }}">
    @csrf
    <div class="form-group">
        <label for="code">{{ __('messages.verification_code') }}</label>
        <input type="text" id="code" name="code" class="code-input" placeholder="000000" maxlength="6" required autofocus>
    </div>
    <button type="submit" class="btn btn-primary">{{ __('messages.verify_email_btn') }}</button>
</form>

<form method="POST" action="{{ route('verification.resend') }}" style="margin-top: 1rem;">
    @csrf
    <div class="resend-link">
        {{ __('messages.didnt_receive_code') }} <button type="submit">{{ __('messages.resend') }}</button>
    </div>
</form>
@endsection
