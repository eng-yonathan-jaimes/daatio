@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('main')
<div class="page-header">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <p style="font-size:1.25rem;color:var(--color-text);margin-bottom:0.25rem;">{{ __('messages.welcome_back_user', ['name' => auth()->user()->name]) }}</p>
        </div>
        <a href="{{ route('transactions.quick') }}" class="btn btn-primary" style="font-size:1rem;padding:0.75rem 1.5rem;">{{ __('messages.new_transaction') }}</a>
    </div>
</div>

@if($subscription)
<div class="alert alert-{{ $subscription->isExpired() ? 'error' : ($subscription->daysRemaining() <= 7 ? 'error' : 'info') }}" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
    <div>
        <strong>{{ $subscription->subscription->subscription_type ?? 'N/A' }}</strong> plan
        &mdash;
        @if($subscription->isExpired())
            <span style="color:#991B1B;">Expired</span>
        @else
            {{ $subscription->daysRemaining() }} day{{ $subscription->daysRemaining() !== 1 ? 's' : '' }} remaining
        @endif
    </div>
    <a href="{{ route('subscription.index') }}" class="btn {{ $subscription->isExpired() || $subscription->daysRemaining() <= 7 ? 'btn-primary' : 'btn-outline' }}" style="font-size:0.85rem;">
        {{ $subscription->isExpired() ? 'Renew Now' : 'Manage' }}
    </a>
</div>
@endif

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">{{ __('messages.total_customers') }}</div>
        <div class="stat-value">{{ $totalCustomers }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">{{ __('messages.total_debt') }}</div>
        <div class="stat-value">${{ number_format($totalDebt, 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">{{ __('messages.total_paid') }}</div>
        <div class="stat-value">${{ number_format($totalPaid, 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">{{ __('messages.outstanding_balance') }}</div>
        <div class="stat-value">${{ number_format($outstandingBalance, 2) }}</div>
    </div>
</div>

<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header">
        <span class="card-title">{{ __('messages.customers_outstanding_debt') }}</span>
    </div>
    <form method="GET" action="{{ route('dashboard') }}" style="margin-bottom:1rem;">
        <div style="display:flex;gap:0.5rem;">
            <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('messages.search_by_name_or_phone') }}" style="flex:1;padding:0.625rem 0.75rem;border:1px solid var(--color-border);border-radius:8px;font-family:var(--font-body);font-size:0.9rem;">
            <button type="submit" class="btn btn-outline">{{ __('messages.search') }}</button>
            @if($search)
                <a href="{{ route('dashboard') }}" class="btn btn-outline">{{ __('messages.clear') }}</a>
            @endif
        </div>
    </form>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.phone') }}</th>
                    <th>{{ __('messages.amount_owed') }}</th>
                    <th>{{ __('messages.last_transaction') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($debtors as $client)
                    @php $debtState = $client->states->first(); @endphp
                    <tr>
                        <td>{{ $client->client_name }} {{ $client->client_last_name }}</td>
                        <td>{{ $client->client_phone_number }}</td>
                        <td>${{ number_format(abs($debtState->client_state_amount ?? 0), 2) }}</td>
                        <td>{{ $debtState->client_state_last_transaction_date?->format('M d, Y') ?? __('messages.none') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center;color:var(--color-text-muted);padding:2rem;">
                            {{ $search ? __('messages.no_customers_found', ['search' => $search]) : __('messages.no_customers_debt') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
