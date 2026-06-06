@extends('layouts.dashboard')

@section('title', 'Reports')

@section('main')
<div class="page-header">
    <h1>Reports</h1>
    <p>View summaries and print records</p>
</div>

<div class="stats-grid" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-label">Total Transactions</div>
        <div class="stat-value">{{ \Modules\Transactions\app\Models\Transaction::count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Customers</div>
        <div class="stat-value">{{ \Modules\Clients\app\Models\Client::where('client_active', true)->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Outstanding Debt</div>
        <div class="stat-value" style="color:#DC2626;">${{ number_format(abs(\Modules\Clients\app\Models\ClientState::where('client_state_state', 'Debit')->sum('client_state_amount')), 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Favorable Balances</div>
        <div class="stat-value" style="color:var(--color-primary);">${{ number_format(\Modules\Clients\app\Models\ClientState::where('client_state_state', 'Favor')->sum('client_state_amount'), 2) }}</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.5rem;">
    <div class="card" style="text-align:center;">
        <div class="card-title" style="margin-bottom:1rem;">Customer List</div>
        <p style="color:var(--color-text-muted);margin-bottom:1.5rem;">All customers with their current balances and states.</p>
        <a href="{{ route('print.customers') }}" target="_blank" class="btn btn-primary">Print</a>
    </div>

    <div class="card" style="text-align:center;">
        <div class="card-title" style="margin-bottom:1rem;">Debt History</div>
        <p style="color:var(--color-text-muted);margin-bottom:1.5rem;">All registered debts across customers.</p>
        <a href="{{ route('print.transactions', ['type' => 'Selling']) }}" target="_blank" class="btn btn-primary">Print</a>
    </div>

    <div class="card" style="text-align:center;">
        <div class="card-title" style="margin-bottom:1rem;">Payment History</div>
        <p style="color:var(--color-text-muted);margin-bottom:1.5rem;">All payments received from customers.</p>
        <a href="{{ route('print.transactions', ['type' => 'Paying']) }}" target="_blank" class="btn btn-primary">Print</a>
    </div>

    <div class="card" style="text-align:center;">
        <div class="card-title" style="margin-bottom:1rem;">Purchase History</div>
        <p style="color:var(--color-text-muted);margin-bottom:1.5rem;">All metal purchase records.</p>
        <a href="{{ route('print.transactions', ['type' => 'Buying']) }}" target="_blank" class="btn btn-primary">Print</a>
    </div>

    <div class="card" style="text-align:center;">
        <div class="card-title" style="margin-bottom:1rem;">Money Deliveries</div>
        <p style="color:var(--color-text-muted);margin-bottom:1.5rem;">All money deliveries to customers.</p>
        <a href="{{ route('print.transactions', ['type' => 'Retriving']) }}" target="_blank" class="btn btn-primary">Print</a>
    </div>

    <div class="card" style="text-align:center;">
        <div class="card-title" style="margin-bottom:1rem;">Full Audit Log</div>
        <p style="color:var(--color-text-muted);margin-bottom:1.5rem;">Every transaction across all types.</p>
        <a href="{{ route('print.transactions') }}" target="_blank" class="btn btn-primary">Print</a>
    </div>
</div>
@endsection
