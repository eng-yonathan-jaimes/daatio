@extends('layouts.dashboard')

@section('title', __('messages.transactions'))

@section('main')
<div class="page-header">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h1>{{ __('messages.transactions') }}</h1>
            <p>{{ __('messages.all_operations') }}</p>
        </div>
        <a href="{{ route('print.transactions', $type ? ['type' => $type] : []) }}" target="_blank" class="btn btn-outline">{{ __('messages.print') }}</a>
    </div>
</div>

<div class="card" style="margin-bottom:1.5rem;">
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
        <a href="{{ route('transactions.index') }}" class="btn {{ !$type ? 'btn-primary' : 'btn-outline' }}" style="font-size:0.85rem;">{{ __('messages.all') }}</a>
        <a href="{{ route('transactions.index', ['type' => 'Selling']) }}" class="btn {{ $type === 'Selling' ? 'btn-primary' : 'btn-outline' }}" style="font-size:0.85rem;">{{ __('messages.purchases') }}</a>
        <a href="{{ route('transactions.index', ['type' => 'Paying']) }}" class="btn {{ $type === 'Paying' ? 'btn-primary' : 'btn-outline' }}" style="font-size:0.85rem;">{{ __('messages.payments') }}</a>
        <a href="{{ route('transactions.index', ['type' => 'Buying']) }}" class="btn {{ $type === 'Buying' ? 'btn-primary' : 'btn-outline' }}" style="font-size:0.85rem;">{{ __('messages.debts') }}</a>
        <a href="{{ route('transactions.index', ['type' => 'Retriving']) }}" class="btn {{ $type === 'Retriving' ? 'btn-primary' : 'btn-outline' }}" style="font-size:0.85rem;">{{ __('messages.deliveries') }}</a>
        <a href="{{ route('transactions.index', ['type' => 'Settle']) }}" class="btn {{ $type === 'Settle' ? 'btn-primary' : 'btn-outline' }}" style="font-size:0.85rem;">{{ __('messages.settlements') }}</a>
    </div>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.description') }}</th>
                    <th>{{ __('messages.customers') }}</th>
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
                        <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $tx->transaction_description ?? __('messages.none') }}</td>
                        <td><a href="{{ route('customers.show', $tx->transaction_client_id) }}" style="color:var(--color-primary);text-decoration:none;">{{ $tx->client?->client_name }} {{ $tx->client?->client_last_name }}</a></td>
                        <td><span class="badge badge-{{ $tx->transaction_transaction === 'Paying' || $tx->transaction_transaction === 'Settle' ? 'success' : ($tx->transaction_transaction === 'Selling' ? 'error' : ($tx->transaction_transaction === 'Retriving' ? 'warning' : 'info')) }}">{{ __('messages.tx_' . strtolower($tx->transaction_transaction)) }}</span></td>
                        <td>${{ number_format($tx->transaction_amount, 2) }}</td>
                        <td>{{ __('messages.' . strtolower($tx->transaction_state)) }}</td>
                        <td style="color:var(--color-text-muted);font-size:0.85rem;">{{ $tx->user?->user_name ?? __('messages.none') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;color:var(--color-text-muted);padding:2rem;">{{ __('messages.no_transactions_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
