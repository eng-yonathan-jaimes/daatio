@extends('print.layout')

@section('title', $client->client_name . ' ' . $client->client_last_name)

@section('content')
<div class="print-header">
    <div>
        <h1>{{ $client->client_name }} {{ $client->client_last_name }}</h1>
        <p style="color:#718096;font-size:0.9rem;">{{ __('messages.customer_history_report') }}</p>
    </div>
    <div class="print-date">{{ __('messages.generated', ['date' => now()->format('M d, Y h:i A')]) }}</div>
</div>

<div class="stats-grid">
    <div class="stat-card"><div class="stat-label">{{ __('messages.phone') }}</div><div class="stat-value" style="font-size:1rem;">{{ $client->client_phone_number }}</div></div>
    <div class="stat-card"><div class="stat-label">{{ __('messages.email_address') }}</div><div class="stat-value" style="font-size:1rem;">{{ $client->client_email ?: __('messages.none') }}</div></div>
    <div class="stat-card"><div class="stat-label">{{ __('messages.document') }}</div><div class="stat-value" style="font-size:1rem;">{{ $client->client_document_number ? $client->client_document_type . ': ' . $client->client_document_number : __('messages.none') }}</div></div>
    <div class="stat-card"><div class="stat-label">{{ __('messages.balance') }}</div><div class="stat-value" style="font-size:1rem;">${{ number_format(abs($balance->client_state_amount ?? 0), 2) }} ({{ __(strtolower($balance->client_state_state ?? 'Settled')) }})</div></div>
</div>

<h2 style="font-size:1rem;margin-bottom:0.75rem;">{{ __('messages.transaction_history') }}</h2>
<table>
    <thead>
        <tr>
            <th>{{ __('messages.date') }}</th>
            <th>{{ __('messages.type') }}</th>
            <th>{{ __('messages.amount') }}</th>
            <th>{{ __('messages.state') }}</th>
            <th>{{ __('messages.by') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse($client->transactions as $tx)
            <tr>
                <td>{{ $tx->transaction_registration_date->format('M d, Y h:i A') }}</td>
                <td>{{ __('messages.tx_' . strtolower($tx->transaction_transaction)) }}</td>
                <td>${{ number_format($tx->transaction_amount, 2) }}</td>
                <td>{{ __(strtolower($tx->transaction_state)) }}</td>
                <td style="font-size:0.8rem;color:#718096;">{{ $tx->user?->user_name ?? __('messages.none') }}</td>
            </tr>
        @empty
            <tr><td colspan="5" style="text-align:center;color:#718096;">{{ __('messages.no_transactions_recorded') }}</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
