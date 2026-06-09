@extends('tenant.layout')

@section('tab-content')
@php
$activeSubscription = \Modules\Subscriptions\app\Models\UserSubscription::where('user_subscription_user_id', auth()->id())
    ->with('subscription', 'payments')
    ->latest('user_subscription_start_date')
    ->first();
$plans = \Modules\Subscriptions\app\Models\Subscription::where('subscription_enabled', true)
    ->where('subscription_type', '!=', 'Trial')
    ->get();
@endphp

<div class="stats-grid" style="margin-bottom:2rem;">
    <div class="stat-card">
        <div class="stat-label">{{ __('messages.current_plan') }}</div>
        <div class="stat-value" style="font-size:1.5rem;">{{ $activeSubscription?->subscription?->subscription_type ?? __('messages.none') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">{{ __('messages.status') }}</div>
        <div class="stat-value" style="font-size:1.25rem;">
            <span class="badge badge-{{ ($activeSubscription?->user_subscription_status ?? 'Expired') === 'Active' ? 'success' : 'error' }}">{{ $activeSubscription?->user_subscription_status ?? __('messages.expired') }}</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label">{{ __('messages.days_remaining') }}</div>
        <div class="stat-value" style="font-size:1.5rem;color:{{ ($activeSubscription?->daysRemaining() ?? 0) <= 7 ? '#DC2626' : 'var(--color-text)' }};">{{ $activeSubscription?->daysRemaining() ?? 0 }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">{{ __('messages.price') }}</div>
        <div class="stat-value" style="font-size:1.25rem;">${{ number_format($activeSubscription?->user_subscription_value ?? 0, 2) }}</div>
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
@endif

@if($activeSubscription?->payments->count())
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
                </tr>
            </thead>
            <tbody>
                @foreach($activeSubscription->payments as $payment)
                    <tr>
                        <td>{{ $payment->subscription_payment_date->format('M d, Y') }}</td>
                        <td>${{ number_format($payment->subscription_payment_amount, 2) }}</td>
                        <td>{{ $payment->subscription_payment_method }}</td>
                        <td><span class="badge badge-{{ $payment->subscription_payment_status === 'Completed' ? 'success' : 'warning' }}">{{ $payment->subscription_payment_status }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
