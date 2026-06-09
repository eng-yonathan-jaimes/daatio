@php
$typeLabels = ['Selling' => 'purchase_history_print', 'Buying' => 'debt_history_print', 'Paying' => 'payment_history_print', 'Retriving' => 'delivery_history_print'];
$title = $type ? ($typeLabels[$type] ?? 'full_transaction_history') : 'full_transaction_history';
@endphp
@extends('print.layout')

@section('title', __('messages.' . $title))

@section('content')
<div class="print-header">
    <div>
        <h1>{{ __('messages.' . $title) }}</h1>
        <p style="color:#718096;font-size:0.9rem;">{{ __('messages.all_operations_print') }}</p>
    </div>
    <div class="print-date">{{ __('messages.generated', ['date' => now()->format('M d, Y h:i A')]) }}</div>
</div>

<table>
    <thead>
        <tr>
            <th>{{ __('messages.date') }}</th>
            <th>{{ __('messages.client') }}</th>
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
                <td>{{ $tx->client?->client_name }} {{ $tx->client?->client_last_name }}</td>
                <td>{{ __('messages.tx_' . strtolower($tx->transaction_transaction)) }}</td>
                <td>${{ number_format($tx->transaction_amount, 2) }}</td>
                <td>{{ __(strtolower($tx->transaction_state)) }}</td>
                <td style="font-size:0.8rem;color:#718096;">{{ $tx->user?->user_name ?? __('messages.none') }}</td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center;color:#718096;">{{ __('messages.no_transactions_recorded') }}</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
