@extends('layouts.dashboard')

@section('title', __('messages.reports'))

@section('main')
<div class="page-header">
    <h1>{{ __('messages.reports') }}</h1>
    <p>{{ __('messages.view_summaries') }}</p>
</div>

<div class="stats-grid" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-label">{{ __('messages.total_transactions') }}</div>
        <div class="stat-value">{{ \Modules\Transactions\app\Models\Transaction::count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">{{ __('messages.total_customers') }}</div>
        <div class="stat-value">{{ \Modules\Clients\app\Models\Client::where('client_active', true)->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">{{ __('messages.outstanding_balance') }}</div>
        <div class="stat-value" style="color:#DC2626;">${{ number_format(abs(\Modules\Clients\app\Models\ClientState::where('client_state_state', 'Debit')->sum('client_state_amount')), 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">{{ __('messages.favorable_balances') }}</div>
        <div class="stat-value" style="color:var(--color-primary);">${{ number_format(\Modules\Clients\app\Models\ClientState::where('client_state_state', 'Favor')->sum('client_state_amount'), 2) }}</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.5rem;">
    <div class="card" style="text-align:center;">
        <div class="card-title" style="margin-bottom:1rem;">{{ __('messages.customer_list') }}</div>
        <p style="color:var(--color-text-muted);margin-bottom:1.5rem;">{{ __('messages.customer_list_desc') }}</p>
        <a href="{{ route('print.customers') }}" target="_blank" class="btn btn-primary">{{ __('messages.print') }}</a>
    </div>
    <div class="card" style="text-align:center;">
        <div class="card-title" style="margin-bottom:1rem;">{{ __('messages.debt_history') }}</div>
        <p style="color:var(--color-text-muted);margin-bottom:1.5rem;">{{ __('messages.debt_history_desc') }}</p>
        <a href="{{ route('print.transactions', ['type' => 'Selling']) }}" target="_blank" class="btn btn-primary">{{ __('messages.print') }}</a>
    </div>
    <div class="card" style="text-align:center;">
        <div class="card-title" style="margin-bottom:1rem;">{{ __('messages.payment_history_report') }}</div>
        <p style="color:var(--color-text-muted);margin-bottom:1.5rem;">{{ __('messages.payment_history_desc') }}</p>
        <a href="{{ route('print.transactions', ['type' => 'Paying']) }}" target="_blank" class="btn btn-primary">{{ __('messages.print') }}</a>
    </div>
    <div class="card" style="text-align:center;">
        <div class="card-title" style="margin-bottom:1rem;">{{ __('messages.purchase_history') }}</div>
        <p style="color:var(--color-text-muted);margin-bottom:1.5rem;">{{ __('messages.purchase_history_desc') }}</p>
        <a href="{{ route('print.transactions', ['type' => 'Buying']) }}" target="_blank" class="btn btn-primary">{{ __('messages.print') }}</a>
    </div>
    <div class="card" style="text-align:center;">
        <div class="card-title" style="margin-bottom:1rem;">{{ __('messages.money_deliveries') }}</div>
        <p style="color:var(--color-text-muted);margin-bottom:1.5rem;">{{ __('messages.money_deliveries_desc') }}</p>
        <a href="{{ route('print.transactions', ['type' => 'Retriving']) }}" target="_blank" class="btn btn-primary">{{ __('messages.print') }}</a>
    </div>
    <div class="card" style="text-align:center;">
        <div class="card-title" style="margin-bottom:1rem;">{{ __('messages.full_audit_log') }}</div>
        <p style="color:var(--color-text-muted);margin-bottom:1.5rem;">{{ __('messages.full_audit_log_desc') }}</p>
        <a href="{{ route('print.transactions') }}" target="_blank" class="btn btn-primary">{{ __('messages.print') }}</a>
    </div>
</div>
@endsection
