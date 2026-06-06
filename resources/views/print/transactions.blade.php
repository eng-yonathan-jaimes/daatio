@extends('print.layout')

@section('title', $type ? ucfirst($type) . ' History' : 'Transaction History')

@section('content')
<div class="print-header">
    <div>
        <h1>{{ $type ? ucfirst($type) . ' History' : 'Full Transaction History' }}</h1>
        <p style="color:#718096;font-size:0.9rem;">All recorded financial operations</p>
    </div>
    <div class="print-date">Generated: {{ now()->format('M d, Y h:i A') }}</div>
</div>

<table>
    <thead>
        <tr>
            <th>Date</th>
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
                <td>{{ $tx->client?->client_name }} {{ $tx->client?->client_last_name }}</td>
                <td>{{ $tx->transaction_transaction }}</td>
                <td>${{ number_format($tx->transaction_amount, 2) }}</td>
                <td>{{ $tx->transaction_state }}</td>
                <td style="font-size:0.8rem;color:#718096;">{{ $tx->user?->user_name ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center;color:#718096;">No transactions found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
