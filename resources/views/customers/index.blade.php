@extends('layouts.dashboard')

@section('title', 'Customers')

@section('main')
<div class="page-header">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h1>Customers</h1>
            <p>Manage your customer records</p>
        </div>
        <div style="display:flex;gap:0.5rem;">
            <a href="{{ route('print.customers') }}" target="_blank" class="btn btn-outline">Print</a>
            <a href="{{ route('customers.create') }}" class="btn btn-primary">+ New Customer</a>
        </div>
    </div>
</div>

@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="card" style="margin-bottom:1.5rem;">
    <form method="GET" action="{{ route('customers.index') }}">
        <div style="display:flex;gap:0.5rem;">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or phone..." style="flex:1;padding:0.625rem 0.75rem;border:1px solid var(--color-border);border-radius:8px;font-family:var(--font-body);font-size:0.9rem;">
            <button type="submit" class="btn btn-outline">Search</button>
            @if($search)
                <a href="{{ route('customers.index') }}" class="btn btn-outline">Clear</a>
            @endif
        </div>
    </form>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Registration Date</th>
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
                            <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-outline" style="padding:0.375rem 0.75rem;font-size:0.8rem;">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center;color:var(--color-text-muted);padding:2rem;">
                            @if($search)
                                No customers found matching "{{ $search }}".
                            @else
                                No customers yet. <a href="{{ route('customers.create') }}">Create your first customer</a>.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
