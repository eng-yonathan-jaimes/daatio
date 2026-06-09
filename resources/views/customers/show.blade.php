@extends('layouts.dashboard')

@section('title', $client->client_name . ' ' . $client->client_last_name)

@section('main')
<div class="page-header">
    <a href="{{ route('customers.index') }}" class="back-link">← {{ __('messages.customers') }}</a>
    <h1>{{ $client->client_name }} {{ $client->client_last_name }}</h1>
</div>

@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

@php
$balanceAmount = $balance->client_state_amount ?? 0;
$balanceColor = $balanceAmount > 0 ? 'var(--color-primary)' : ($balanceAmount < 0 ? '#DC2626' : 'var(--color-text)');
$balanceLabel = $balanceAmount > 0 ? __('messages.favor') : ($balanceAmount < 0 ? __('messages.debit') : __('messages.settled'));
$balanceBadge = $balanceAmount > 0 ? 'info' : ($balanceAmount < 0 ? 'error' : 'success');
@endphp

<div class="stats-grid" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-label">{{ __('messages.current_balance') }}</div>
        <div class="stat-value" style="color:{{ $balanceColor }};">
            ${{ number_format(abs($balanceAmount), 2) }}
        </div>
        <div class="stat-change">
            <span class="badge badge-{{ $balanceBadge }}">{{ $balanceLabel }}</span>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:1.5rem;">
    <div style="display:flex;gap:2rem;flex-wrap:wrap;">
        <div>
            <div class="stat-label" style="margin-bottom:0.25rem;">{{ __('messages.phone') }}</div>
            <div style="font-weight:500;">{{ $client->client_phone_number }}</div>
        </div>
        <div>
            <div class="stat-label" style="margin-bottom:0.25rem;">{{ __('messages.email_address') }}</div>
            <div style="font-weight:500;">{{ $client->client_email ?: __('messages.none') }}</div>
        </div>
        <div>
            <div class="stat-label" style="margin-bottom:0.25rem;">{{ __('messages.document') }}</div>
            <div style="font-weight:500;">
                @if($client->client_document_number)
                    {{ $client->client_document_type }}: {{ $client->client_document_number }}
                @else
                    {{ __('messages.none') }}
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title" style="margin-bottom:1rem;">{{ __('messages.actions') }}</div>
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
        <a href="{{ route('transactions.create', ['customer' => $client->id, 'type' => 'Buying']) }}" class="btn btn-outline">{{ __('messages.add_debt') }}</a>
        <a href="{{ route('transactions.create', ['customer' => $client->id, 'type' => 'Paying']) }}" class="btn btn-outline">{{ __('messages.register_payment') }}</a>
        <a href="{{ route('transactions.create', ['customer' => $client->id, 'type' => 'Selling']) }}" class="btn btn-outline">{{ __('messages.metal_purchase') }}</a>
        <a href="{{ route('transactions.create', ['customer' => $client->id, 'type' => 'Retriving']) }}" class="btn btn-outline">{{ __('messages.deliver_money') }}</a>
        @if($balanceAmount != 0)
            <a href="{{ route('transactions.create', ['customer' => $client->id, 'type' => 'Settle']) }}" class="btn btn-outline" style="color:#DC2626;border-color:#DC2626;">{{ __('messages.settle_account') }}</a>
        @endif
        <a href="{{ route('print.customer', $client->id) }}" target="_blank" class="btn btn-outline" style="margin-left:auto;">{{ __('messages.print_history') }}</a>
    </div>
</div>

@if($client->orders->count())
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title" style="margin-bottom:1rem;">{{ __('messages.orders') }}</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.state') }}</th>
                    <th>{{ __('messages.value') }}</th>
                    <th>{{ __('messages.items') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($client->orders as $order)
                    <tr>
                        <td>{{ $order->client_order_registration_date->format('M d, Y') }}</td>
                        <td>
                            <span class="badge badge-{{ $order->client_order_state === 'Debit' ? 'error' : ($order->client_order_state === 'Favor' ? 'info' : 'success') }}">
                                {{ $order->client_order_state === 'Debit' ? __('messages.debit') : ($order->client_order_state === 'Favor' ? __('messages.favor') : __('messages.settled')) }}
                            </span>
                        </td>
                        <td>${{ number_format($order->client_order_value, 2) }}</td>
                        <td>
                            @if($order->items->count())
                                @foreach($order->items as $item)
                                    <div style="font-size:0.85rem;color:var(--color-text-muted);">
                                        {{ $item->product?->product_name ?? __('messages.unknown') }}
                                        @if($item->client_list_order_weight > 0)
                                            — {{ rtrim(rtrim(number_format($item->client_list_order_weight, 4), '0'), '.') }}g
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <span style="color:var(--color-text-muted);">{{ __('messages.none') }}</span>
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
        <span class="card-title">{{ __('messages.transaction_history') }}</span>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.description') }}</th>
                    <th>{{ __('messages.type') }}</th>
                    <th>{{ __('messages.amount') }}</th>
                    <th>{{ __('messages.state') }}</th>
                    <th>{{ __('messages.by') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                    <tr>
                        <td>{{ $tx->transaction_registration_date->format('M d, Y h:i A') }}</td>
                        <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $tx->transaction_description ?? __('messages.none') }}</td>
                        <td>
                            <span class="badge badge-{{ $tx->transaction_transaction === 'Paying' || $tx->transaction_transaction === 'Settle' ? 'success' : ($tx->transaction_transaction === 'Selling' ? 'error' : ($tx->transaction_transaction === 'Retriving' ? 'warning' : 'info')) }}">
                                {{ __('messages.tx_' . strtolower($tx->transaction_transaction)) }}
                            </span>
                        </td>
                        <td>${{ number_format($tx->transaction_amount, 2) }}</td>
                        <td>{{ __('messages.' . strtolower($tx->transaction_state)) }}</td>
                        <td style="color:var(--color-text-muted);font-size:0.85rem;">{{ $tx->user?->user_name ?? __('messages.none') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;color:var(--color-text-muted);padding:2rem;">
                            {{ __('messages.no_transactions') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
