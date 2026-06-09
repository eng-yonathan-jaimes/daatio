@extends('layouts.dashboard')

@section('title', __('messages.my_account'))

@section('main')
<div class="page-header">
    <h1>{{ __('messages.my_account') }}</h1>
    <p>{{ auth()->user()->user_email }}</p>
</div>

@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

@if($errors->any() && !$errors->has('current_password') && !$errors->has('new_password'))
    <div class="alert alert-error">{{ $errors->first() }}</div>
@endif

<div class="card" style="margin-bottom:1.5rem;padding:0;">
    <div style="display:flex;border-bottom:1px solid var(--color-border);">
        <a href="{{ route('account.profile.edit') }}" class="tab-link {{ request()->routeIs('account.profile.*') ? 'tab-active' : '' }}">{{ __('messages.profile') }}</a>
        <a href="{{ route('account.security.index') }}" class="tab-link {{ request()->routeIs('account.security.*') ? 'tab-active' : '' }}">{{ __('messages.security_logs') }}</a>
        <a href="{{ route('account.subscription.index') }}" class="tab-link {{ request()->routeIs('account.subscription.*') ? 'tab-active' : '' }}">{{ __('messages.subscription') }}</a>
    </div>
    <div style="padding:1.5rem;">
        @yield('tab-content')
    </div>
</div>
@endsection
