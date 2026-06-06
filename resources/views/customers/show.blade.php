@extends('layouts.dashboard')

@section('title', $client->client_name . ' ' . $client->client_last_name)

@section('main')
<div class="page-header">
    <a href="{{ route('customers.index') }}" class="back-link">← Back to customers</a>
    <h1>{{ $client->client_name }} {{ $client->client_last_name }}</h1>
</div>

@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

@php
$balanceAmount = $balance->client_state_amount ?? 0;
$balanceColor = $balanceAmount > 0 ? 'var(--color-primary)' : ($balanceAmount < 0 ? '#DC2626' : 'var(--color-text)');
$balanceLabel = $balanceAmount > 0 ? 'Favor' : ($balanceAmount < 0 ? 'Debit' : 'Settled');
$balanceBadge = $balanceAmount > 0 ? 'info' : ($balanceAmount < 0 ? 'error' : 'success');
@endphp

<div class="stats-grid" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-label">Current Balance</div>
        <div class="stat-value" style="color:{{ $balanceColor }};">
            ${{ number_format(abs($balanceAmount), 2) }}
        </div>
        <div class="stat-change">
            <span class="badge badge-{{ $balanceBadge }}">
                {{ $balanceLabel }}
            </span>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:1.5rem;">
    <div style="display:flex;gap:2rem;flex-wrap:wrap;">
        <div>
            <div class="stat-label" style="margin-bottom:0.25rem;">Phone</div>
            <div style="font-weight:500;">{{ $client->client_phone_number }}</div>
        </div>
        <div>
            <div class="stat-label" style="margin-bottom:0.25rem;">Email</div>
            <div style="font-weight:500;">{{ $client->client_email ?: '—' }}</div>
        </div>
        <div>
            <div class="stat-label" style="margin-bottom:0.25rem;">Document</div>
            <div style="font-weight:500;">
                @if($client->client_document_number)
                    {{ $client->client_document_type }}: {{ $client->client_document_number }}
                @else
                    —
                @endif
            </div>
        </div>
    </div>
</div>


<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title" style="margin-bottom:1rem;">Actions</div>
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
        <a href="{{ route('transactions.create', ['customer' => $client->id, 'type' => 'Buying']) }}" class="btn btn-outline">+ Add Debt</a>
        <a href="{{ route('transactions.create', ['customer' => $client->id, 'type' => 'Paying']) }}" class="btn btn-outline">+ Register Payment</a>
        <a href="{{ route('transactions.create', ['customer' => $client->id, 'type' => 'Selling']) }}" class="btn btn-outline">+ Metal Purchase</a>
        <a href="{{ route('transactions.create', ['customer' => $client->id, 'type' => 'Retriving']) }}" class="btn btn-outline">+ Deliver Money</a>
        @if($balanceAmount != 0)
            <a href="{{ route('transactions.create', ['customer' => $client->id, 'type' => 'Settle']) }}" class="btn btn-outline" style="color:#DC2626;border-color:#DC2626;">Settle Account</a>
        @endif
        <a href="{{ route('print.customer', $client->id) }}" target="_blank" class="btn btn-outline" style="margin-left:auto;">Print History</a>
    </div>
</div>


@if($client->orders->count())
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title" style="margin-bottom:1rem;">Orders</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>State</th>
                    <th>Value</th>
                    <th>Items</th>
                </tr>
            </thead>
            <tbody>
                @foreach($client->orders as $order)
                    <tr>
                        <td>{{ $order->client_order_registration_date->format('M d, Y') }}</td>
                        <td>
                            <span class="badge badge-{{ $order->client_order_state === 'Debit' ? 'error' : ($order->client_order_state === 'Favor' ? 'info' : 'success') }}">
                                {{ $order->client_order_state }}
                            </span>
                        </td>
                        <td>${{ number_format($order->client_order_value, 2) }}</td>
                        <td>
                            @if($order->items->count())
                                @foreach($order->items as $item)
                                    <div style="font-size:0.85rem;color:var(--color-text-muted);">
                                        {{ $item->product?->product_name ?? 'Unknown' }}
                                        @if($item->client_list_order_weight > 0)
                                            — {{ rtrim(rtrim(number_format($item->client_list_order_weight, 4), '0'), '.') }}g
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <span style="color:var(--color-text-muted);">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header">
        <span class="card-title">Transaction History</span>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
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
                        <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $tx->transaction_description ?? '-' }}</td>
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
                        <td colspan="4" style="text-align:center;color:var(--color-text-muted);padding:2rem;">
                            No transactions yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
