@extends('layouts.app')

@section('content')
<nav class="navbar">
    <a href="/daatio/public/" class="navbar-brand">Daatio</a>
    <div class="navbar-menu">
        <a href="/daatio/public/" class="active">Home</a>
        <a href="#">Features</a>
        <a href="#">Pricing</a>
        <a href="#">About</a>
    </div>
    <div class="navbar-actions">
        <a href="/daatio/public/login" class="btn btn-outline">Sign in</a>
        <a href="/daatio/public/register" class="btn btn-primary">Get Started</a>
    </div>
</nav>

<div class="main-content" style="padding: 0; display: block;">
    <div class="landing-hero">
        <h1>Simplify Your Business, Elevate Your Success</h1>
        <p>Daatio helps small businesses manage finances, track customers, and generate insights — all in one intuitive platform.</p>
        <a href="/daatio/public/register" class="btn btn-primary">Get Started Free</a>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">◉</div>
                <h3>Dashboard</h3>
                <p>Real-time overview of your business performance at a glance.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">◎</div>
                <h3>Cashbook</h3>
                <p>Track every transaction with easy-to-use cash management tools.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">◐</div>
                <h3>Customers</h3>
                <p>Manage customer relationships and track payment history.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">◑</div>
                <h3>Suppliers</h3>
                <p>Keep your supplier information organized and accessible.</p>
            </div>
        </div>
    </div>
</div>
@endsection
