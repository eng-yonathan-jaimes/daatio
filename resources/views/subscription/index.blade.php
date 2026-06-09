@extends('layouts.dashboard')

@section('title', __('messages.subscription'))

@section('main')
<div class="page-header">
    <h1>{{ __('messages.subscription') }}</h1>
    <p>{{ __('messages.manage_subscription') }}</p>
</div>

@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="stats-grid" style="margin-bottom:2rem;">
    <div class="stat-card">
        <div class="stat-label">{{ __('messages.current_plan') }}</div>
        <div class="stat-value" style="font-size:1.5rem;">
            {{ $activeSubscription?->subscription?->subscription_type ?? __('messages.none') }}
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label">{{ __('messages.status') }}</div>
        <div class="stat-value" style="font-size:1.25rem;">
            <span class="badge badge-{{ ($activeSubscription?->user_subscription_status ?? 'Expired') === 'Active' ? 'success' : (($activeSubscription?->user_subscription_status ?? 'Expired') === 'Trial' ? 'info' : 'error') }}">
                {{ __($activeSubscription?->user_subscription_status ?? 'Expired') }}
            </span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label">{{ __('messages.days_remaining') }}</div>
        <div class="stat-value" style="font-size:1.5rem;color:{{ ($activeSubscription?->daysRemaining() ?? 0) <= 7 ? '#DC2626' : 'var(--color-text)' }};">
            {{ $activeSubscription?->daysRemaining() ?? 0 }}
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label">{{ __('messages.price') }}</div>
        <div class="stat-value" style="font-size:1.25rem;">
            ${{ number_format($activeSubscription?->user_subscription_value ?? 0, 2) }}
        </div>
    </div>
</div>

@if($activeSubscription)
<div class="card" style="margin-bottom:2rem;">
    <div class="card-title" style="margin-bottom:1rem;">{{ __('messages.plan_details') }}</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:1rem;">
        <div><div class="stat-label">{{ __('messages.started') }}</div><div style="font-weight:500;">{{ $activeSubscription->user_subscription_start_date->format('M d, Y') }}</div></div>
        <div><div class="stat-label">{{ __('messages.expires') }}</div><div style="font-weight:500;">{{ $activeSubscription->user_subscription_end_date->format('M d, Y') }}</div></div>
        <div><div class="stat-label">{{ __('messages.max_stores') }}</div><div style="font-weight:500;">{{ $activeSubscription->subscription->subscription_max_stores ?? 1 }}</div></div>
    </div>
</div>

@if($activeSubscription->payments->count())
<div class="card" style="margin-bottom:2rem;">
    <div class="card-title" style="margin-bottom:1rem;">{{ __('messages.payment_history') }}</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.amount') }}</th>
                    <th>{{ __('messages.method') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.reference') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activeSubscription->payments as $payment)
                    <tr>
                        <td>{{ $payment->subscription_payment_date->format('M d, Y') }}</td>
                        <td>${{ number_format($payment->subscription_payment_amount, 2) }}</td>
                        <td>{{ $payment->subscription_payment_method }}</td>
                        <td><span class="badge badge-{{ $payment->subscription_payment_status === 'Completed' ? 'success' : 'warning' }}">{{ __($payment->subscription_payment_status) }}</span></td>
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
    <div class="card-title" style="margin-bottom:1rem;">{{ __('messages.available_plans') }}</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1rem;">
        @foreach($plans as $plan)
            <div style="border:1px solid var(--color-border);border-radius:8px;padding:1.5rem;text-align:center;">
                <h3 style="font-family:var(--font-heading);margin-bottom:0.5rem;">{{ $plan->subscription_type }}</h3>
                @if($plan->subscription_description)
                    <p style="color:var(--color-text-muted);font-size:0.875rem;margin-bottom:1rem;">{{ $plan->subscription_description }}</p>
                @endif
                <div style="font-size:2rem;font-weight:700;font-family:var(--font-heading);margin-bottom:0.5rem;">${{ number_format($plan->subscription_value, 2) }}</div>
                <div style="color:var(--color-text-muted);font-size:0.875rem;margin-bottom:1rem;">{{ __('messages.per') }} {{ $plan->subscription_period }} ({{ $plan->subscription_days }} {{ __('messages.days') }})</div>
                <div style="color:var(--color-text-muted);font-size:0.8rem;margin-bottom:1rem;">{{ __('messages.max_stores') }}: {{ $plan->subscription_max_stores }}</div>
                <form method="POST" action="{{ route('subscription.renew') }}" onsubmit="return confirm('{{ __('messages.activate_plan_confirm', ['plan' => $plan->subscription_type, 'price' => number_format($plan->subscription_value, 2)]) }}')">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <button type="submit" class="btn btn-primary" style="width:100%;">{{ $activeSubscription?->subscription?->id === $plan->id ? __('messages.current_plan_btn') : __('messages.choose_plan') }}</button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
