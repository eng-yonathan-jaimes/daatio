<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Daatio</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <a href="/daatio/public/" class="navbar-brand">Daatio</a>
        <div class="navbar-actions">
            <span style="color:var(--color-text-muted);font-size:0.875rem;margin-right:0.5rem;">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-outline">Logout</button>
            </form>
        </div>
    </nav>

    <div class="page-container">
        <aside class="sidebar">
            <div class="sidebar-section">
                <div class="sidebar-title">Main</div>
                <ul class="sidebar-menu">
                    <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><span class="icon">◉</span> Dashboard</a></li>
                    <li><a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}"><span class="icon">◐</span> Customers</a></li>
                    <li><a href="{{ route('transactions.index') }}" class="{{ request()->routeIs('transactions.*') ? 'active' : '' }}"><span class="icon">◎</span> Transactions</a></li>
                    <li><a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}"><span class="icon">◆</span> Products</a></li>
                    <li><a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}"><span class="icon">▤</span> Reports</a></li>
                </ul>
            </div>
            <div class="sidebar-section">
                <div class="sidebar-title">Account</div>
                <ul class="sidebar-menu">
                    <li><a href="{{ route('stores.index') }}" class="{{ request()->routeIs('stores.*') ? 'active' : '' }}"><span class="icon">⌂</span> Stores</a></li>
                    <li><a href="{{ route('subscription.index') }}" class="{{ request()->routeIs('subscription.*') ? 'active' : '' }}"><span class="icon">◇</span> Subscription</a></li>
                </ul>
            </div>
        </aside>

        <main class="main-content">
            @yield('main')
        </main>
    </div>

    <footer style="text-align:center;padding:1.5rem;color:var(--color-text-muted);font-size:0.8rem;border-top:0.5px solid var(--color-border);">
        &copy; {{ date('Y') }} Daatio. All rights reserved.
    </footer>
</body>
</html>
