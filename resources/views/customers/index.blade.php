@extends('layouts.dashboard')

@section('title', __('messages.customers'))

@section('main')
<div class="page-header">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h1>{{ __('messages.customers') }}</h1>
            <p>{{ __('messages.manage_customers') }}</p>
        </div>
        <div style="display:flex;gap:0.5rem;">
            <a href="{{ route('print.customers') }}" target="_blank" class="btn btn-outline">{{ __('messages.print') }}</a>
            <a href="{{ route('customers.create') }}" class="btn btn-primary">{{ __('messages.new_customer') }}</a>
        </div>
    </div>
</div>

@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="card" style="margin-bottom:1.5rem;">
    <form method="GET" action="{{ route('customers.index') }}">
        <div style="display:flex;gap:0.5rem;">
            <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('messages.search_by_name_or_phone') }}" style="flex:1;padding:0.625rem 0.75rem;border:1px solid var(--color-border);border-radius:8px;font-family:var(--font-body);font-size:0.9rem;">
            <button type="submit" class="btn btn-outline">{{ __('messages.search') }}</button>
            @if($search)
                <a href="{{ route('customers.index') }}" class="btn btn-outline">{{ __('messages.clear') }}</a>
            @endif
        </div>
    </form>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.phone') }}</th>
                    <th>{{ __('messages.registration_date') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td><a href="{{ route('customers.show', $customer->id) }}" style="color:var(--color-primary);text-decoration:none;font-weight:500;">{{ $customer->client_name }} {{ $customer->client_last_name }}</a></td>
                        <td>{{ $customer->client_phone_number }}</td>
                        <td>{{ $customer->client_registration_date->format('M d, Y') }}</td>
                        <td style="text-align:right;">
                            <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-outline" style="padding:0.375rem 0.75rem;font-size:0.8rem;">{{ __('messages.view') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center;color:var(--color-text-muted);padding:2rem;">
                            @if($search)
                                {{ __('messages.no_customers_found', ['search' => $search]) }}
                            @else
                                {{ __('messages.no_customers_yet') }} <a href="{{ route('customers.create') }}">{{ __('messages.create_first_customer') }}</a>.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
