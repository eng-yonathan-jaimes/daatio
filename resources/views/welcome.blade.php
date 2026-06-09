@extends('layouts.app')

@section('content')
<nav class="navbar">
    <a href="/daatio/public/" class="navbar-brand">Daatio</a>
    <div class="navbar-menu">
        <a href="/daatio/public/" class="active">{{ __('messages.home') }}</a>
        <a href="#">{{ __('messages.features') }}</a>
        <a href="#">{{ __('messages.pricing') }}</a>
        <a href="#">{{ __('messages.about') }}</a>
    </div>
    <div class="navbar-actions">
        <a href="/daatio/public/login" class="btn btn-outline">{{ __('messages.sign_in') }}</a>
        <a href="/daatio/public/register" class="btn btn-primary">{{ __('messages.get_started_free') }}</a>
    </div>
</nav>

<div class="main-content" style="padding: 0; display: block;">
    <div class="landing-hero">
        <h1>{{ __('messages.welcome_title') }}</h1>
        <p>{{ __('messages.welcome_subtitle') }}</p>
        <a href="/daatio/public/register" class="btn btn-primary">{{ __('messages.get_started_free') }}</a>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">◉</div>
                <h3>{{ __('messages.feature_dashboard') }}</h3>
                <p>{{ __('messages.feature_dashboard_desc') }}</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">◎</div>
                <h3>{{ __('messages.feature_cashbook') }}</h3>
                <p>{{ __('messages.feature_cashbook_desc') }}</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">◐</div>
                <h3>{{ __('messages.feature_customers') }}</h3>
                <p>{{ __('messages.feature_customers_desc') }}</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">◑</div>
                <h3>{{ __('messages.feature_suppliers') }}</h3>
                <p>{{ __('messages.feature_suppliers_desc') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
