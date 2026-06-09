@extends('print.layout')

@section('title', __('messages.customer_list'))

@section('content')
<div class="print-header">
    <div>
        <h1>{{ __('messages.customer_list') }}</h1>
        <p style="color:#718096;font-size:0.9rem;">{{ __('messages.all_customers_balances') }}</p>
    </div>
    <div class="print-date">{{ __('messages.generated', ['date' => now()->format('M d, Y h:i A')]) }}</div>
</div>

<table>
    <thead>
        <tr>
            <th>{{ __('messages.name') }}</th>
            <th>{{ __('messages.phone') }}</th>
            <th>{{ __('messages.balance') }}</th>
            <th>{{ __('messages.state') }}</th>
            <th>{{ __('messages.registered') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse($customers as $customer)
            @php $state = $customer->states->first(); @endphp
            <tr>
                <td>{{ $customer->client_name }} {{ $customer->client_last_name }}</td>
                <td>{{ $customer->client_phone_number }}</td>
                <td>${{ number_format(abs($state->client_state_amount ?? 0), 2) }}</td>
                <td>{{ __(strtolower($state->client_state_state ?? 'Settled')) }}</td>
                <td>{{ $customer->client_registration_date->format('M d, Y') }}</td>
            </tr>
        @empty
            <tr><td colspan="5" style="text-align:center;color:#718096;">{{ __('messages.no_customers_print') }}</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
