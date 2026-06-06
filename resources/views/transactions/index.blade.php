@extends('layouts.dashboard')

@section('title', 'Transactions')

@section('main')
<div class="page-header">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h1>Transactions</h1>
            <p>All financial operations across customers</p>
        </div>
        <a href="{{ route('print.transactions', $type ? ['type' => $type] : []) }}" target="_blank" class="btn btn-outline">Print</a>
    </div>
</div>

<div class="card" style="margin-bottom:1.5rem;">
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
        <a href="{{ route('transactions.index') }}" class="btn {{ !$type ? 'btn-primary' : 'btn-outline' }}" style="font-size:0.85rem;">All</a>
         <a href="{{ route('transactions.index', ['type' => 'Selling']) }}" class="btn {{ $type === 'Selling' ? 'btn-primary' : 'btn-outline' }}" style="font-size:0.85rem;">Purchases</a>
        <a href="{{ route('transactions.index', ['type' => 'Paying']) }}" class="btn {{ $type === 'Paying' ? 'btn-primary' : 'btn-outline' }}" style="font-size:0.85rem;">Payments</a>
        <a href="{{ route('transactions.index', ['type' => 'Buying']) }}" class="btn {{ $type === 'Buying' ? 'btn-primary' : 'btn-outline' }}" style="font-size:0.85rem;">Debts</a>
        <a href="{{ route('transactions.index', ['type' => 'Retriving']) }}" class="btn {{ $type === 'Retriving' ? 'btn-primary' : 'btn-outline' }}" style="font-size:0.85rem;">Deliveries</a>
        <a href="{{ route('transactions.index', ['type' => 'Settle']) }}" class="btn {{ $type === 'Settle' ? 'btn-primary' : 'btn-outline' }}" style="font-size:0.85rem;">Settlements</a>
    </div>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Customer</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>State</th>
                    <th>By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                    <tr>
                        <td>{{ $tx->transaction_registration_date->format('M d, Y h:i A') }}</td>
                        <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $tx->transaction_description ?? '-' }}</td>
                        <td>
                            <a href="{{ route('customers.show', $tx->transaction_client_id) }}" style="color:var(--color-primary);text-decoration:none;">
                                {{ $tx->client?->client_name }} {{ $tx->client?->client_last_name }}
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-{{ $tx->transaction_transaction === 'Paying' || $tx->transaction_transaction === 'Settle' ? 'success' : ($tx->transaction_transaction === 'Selling' ? 'error' : ($tx->transaction_transaction === 'Retriving' ? 'warning' : 'info')) }}">
                                {{ $tx->transaction_transaction }}
                            </span>
                        </td>
                        <td>${{ number_format($tx->transaction_amount, 2) }}</td>
                        <td>{{ $tx->transaction_state }}</td>
                        <td style="color:var(--color-text-muted);font-size:0.85rem;">{{ $tx->user?->user_name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;color:var(--color-text-muted);padding:2rem;">
                            No transactions yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
