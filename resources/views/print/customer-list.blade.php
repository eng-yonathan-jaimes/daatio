@extends('print.layout')

@section('title', 'Customer List')

@section('content')
<div class="print-header">
    <div>
        <h1>Customer List</h1>
        <p style="color:#718096;font-size:0.9rem;">All registered customers with balances</p>
    </div>
    <div class="print-date">Generated: {{ now()->format('M d, Y h:i A') }}</div>
</div>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Phone</th>
            <th>Balance</th>
            <th>State</th>
            <th>Registered</th>
        </tr>
    </thead>
    <tbody>
        @forelse($customers as $customer)
            @php $state = $customer->states->first(); @endphp
            <tr>
                <td>{{ $customer->client_name }} {{ $customer->client_last_name }}</td>
                <td>{{ $customer->client_phone_number }}</td>
                <td>${{ number_format($state->client_state_amount ?? 0, 2) }}</td>
                <td>{{ $state->client_state_state ?? 'Settled' }}</td>
                <td>{{ $customer->client_registration_date->format('M d, Y') }}</td>
            </tr>
        @empty
            <tr><td colspan="5" style="text-align:center;color:#718096;">No customers found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
