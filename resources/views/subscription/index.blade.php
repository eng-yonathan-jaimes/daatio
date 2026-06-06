@extends('layouts.dashboard')

@section('title', 'Subscription')

@section('main')
<div class="page-header">
    <h1>Subscription</h1>
    <p>Manage your plan and billing</p>
</div>

@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="stats-grid" style="margin-bottom:2rem;">
    <div class="stat-card">
        <div class="stat-label">Current Plan</div>
        <div class="stat-value" style="font-size:1.5rem;">
            {{ $activeSubscription?->subscription?->subscription_type ?? 'None' }}
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Status</div>
        <div class="stat-value" style="font-size:1.25rem;">
            <span class="badge badge-{{ ($activeSubscription?->user_subscription_status ?? 'Expired') === 'Active' ? 'success' : (($activeSubscription?->user_subscription_status ?? 'Expired') === 'Trial' ? 'info' : 'error') }}">
                {{ $activeSubscription?->user_subscription_status ?? 'Expired' }}
            </span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Days Remaining</div>
        <div class="stat-value" style="font-size:1.5rem;color:{{ ($activeSubscription?->daysRemaining() ?? 0) <= 7 ? '#DC2626' : 'var(--color-text)' }};">
            {{ $activeSubscription?->daysRemaining() ?? 0 }}
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Price</div>
        <div class="stat-value" style="font-size:1.25rem;">
            ${{ number_format($activeSubscription?->user_subscription_value ?? 0, 2) }}
        </div>
    </div>
</div>

@if($activeSubscription)
<div class="card" style="margin-bottom:2rem;">
    <div class="card-title" style="margin-bottom:1rem;">Plan Details</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:1rem;">
        <div>
            <div class="stat-label">Started</div>
            <div style="font-weight:500;">{{ $activeSubscription->user_subscription_start_date->format('M d, Y') }}</div>
        </div>
        <div>
            <div class="stat-label">Expires</div>
            <div style="font-weight:500;">{{ $activeSubscription->user_subscription_end_date->format('M d, Y') }}</div>
        </div>
        <div>
            <div class="stat-label">Max Stores</div>
            <div style="font-weight:500;">{{ $activeSubscription->subscription->subscription_max_stores ?? 1 }}</div>
        </div>
    </div>
</div>

@if($activeSubscription->payments->count())
<div class="card" style="margin-bottom:2rem;">
    <div class="card-title" style="margin-bottom:1rem;">Payment History</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Reference</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activeSubscription->payments as $payment)
                    <tr>
                        <td>{{ $payment->subscription_payment_date->format('M d, Y') }}</td>
                        <td>${{ number_format($payment->subscription_payment_amount, 2) }}</td>
                        <td>{{ $payment->subscription_payment_method }}</td>
                        <td><span class="badge badge-{{ $payment->subscription_payment_status === 'Completed' ? 'success' : 'warning' }}">{{ $payment->subscription_payment_status }}</span></td>
                        <td style="font-family:var(--font-mono);font-size:0.8rem;">{{ $payment->subscription_payment_reference }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endif

<div class="card">
    <div class="card-title" style="margin-bottom:1rem;">Available Plans</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1rem;">
        @foreach($plans as $plan)
            <div style="border:1px solid var(--color-border);border-radius:8px;padding:1.5rem;text-align:center;">
                <h3 style="font-family:var(--font-heading);margin-bottom:0.5rem;">{{ $plan->subscription_type }}</h3>
                @if($plan->subscription_description)
                    <p style="color:var(--color-text-muted);font-size:0.875rem;margin-bottom:1rem;">{{ $plan->subscription_description }}</p>
                @endif
                <div style="font-size:2rem;font-weight:700;font-family:var(--font-heading);margin-bottom:0.5rem;">
                    ${{ number_format($plan->subscription_value, 2) }}
                </div>
                <div style="color:var(--color-text-muted);font-size:0.875rem;margin-bottom:1rem;">
                    / {{ $plan->subscription_period }} ({{ $plan->subscription_days }} days)
                </div>
                <div style="color:var(--color-text-muted);font-size:0.8rem;margin-bottom:1rem;">
                    Up to {{ $plan->subscription_max_stores }} store{{ $plan->subscription_max_stores > 1 ? 's' : '' }}
                </div>
                <form method="POST" action="{{ route('subscription.renew') }}" onsubmit="return confirm('Activate {{ $plan->subscription_type }} plan for ${{ number_format($plan->subscription_value, 2) }}?')">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <button type="submit" class="btn btn-primary" style="width:100%;">
                        {{ $activeSubscription?->subscription?->id === $plan->id ? 'Current Plan' : 'Choose Plan' }}
                    </button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
