@extends('print.layout')

@section('title', $client->client_name . ' ' . $client->client_last_name)

@section('content')
<div class="print-header">
    <div>
        <h1>{{ $client->client_name }} {{ $client->client_last_name }}</h1>
        <p style="color:#718096;font-size:0.9rem;">Customer History Report</p>
    </div>
    <div class="print-date">Generated: {{ now()->format('M d, Y h:i A') }}</div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Phone</div>
        <div class="stat-value" style="font-size:1rem;">{{ $client->client_phone_number }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Email</div>
        <div class="stat-value" style="font-size:1rem;">{{ $client->client_email ?: '-' }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Document</div>
        <div class="stat-value" style="font-size:1rem;">
            {{ $client->client_document_number ? $client->client_document_type . ': ' . $client->client_document_number : '-' }}
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Balance</div>
        <div class="stat-value" style="font-size:1rem;">${{ number_format($balance->client_state_amount ?? 0, 2) }} ({{ $balance->client_state_state ?? 'Settled' }})</div>
    </div>
</div>

<h2 style="font-size:1rem;margin-bottom:0.75rem;">Transaction History</h2>
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Amount</th>
            <th>State</th>
            <th>By</th>
        </tr>
    </thead>
    <tbody>
        @forelse($client->transactions as $tx)
            <tr>
                <td>{{ $tx->transaction_registration_date->format('M d, Y h:i A') }}</td>
                <td>{{ $tx->transaction_transaction }}</td>
                <td>${{ number_format($tx->transaction_amount, 2) }}</td>
                <td>{{ $tx->transaction_state }}</td>
                <td style="font-size:0.8rem;color:#718096;">{{ $tx->user?->user_name ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="5" style="text-align:center;color:#718096;">No transactions recorded.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
