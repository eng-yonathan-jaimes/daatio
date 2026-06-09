@extends('layouts.dashboard')

@section('title', __('messages.stores'))

@section('main')
<div class="page-header">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h1>{{ __('messages.stores') }}</h1>
            <p>{{ __('messages.manage_stores') }}</p>
        </div>
        <a href="{{ route('stores.create') }}" class="btn btn-primary">{{ __('messages.new_store') }}</a>
    </div>
</div>

@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>{{ __('messages.store_name') }}</th>
                    <th>{{ __('messages.store_type') }}</th>
                    <th>{{ __('messages.location_type') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($stores as $store)
                    <tr>
                        <td style="font-weight:500;">{{ $store->store_name }}</td>
                        <td>{{ $store->type?->store_type_description ?? __('messages.n_a') }}</td>
                        <td>{{ $store->store_location === 'Physical' ? __('messages.physical') : ($store->store_location === 'Online' ? __('messages.online') : __('messages.both')) }}</td>
                        <td><span class="badge badge-{{ $store->store_active ? 'success' : 'error' }}">{{ $store->store_active ? __('messages.active') : __('messages.inactive') }}</span></td>
                        <td style="text-align:right;">
                            <a href="{{ route('stores.edit', $store->id) }}" class="btn btn-outline" style="padding:0.375rem 0.75rem;font-size:0.8rem;">{{ __('messages.edit') }}</a>
                            <form method="POST" action="{{ route('stores.toggle', $store->id) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-outline" style="padding:0.375rem 0.75rem;font-size:0.8rem;">{{ $store->store_active ? __('messages.deactivate') : __('messages.activate') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;color:var(--color-text-muted);padding:2rem;">{{ __('messages.create_first_store') }}.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card" style="margin-top:1.5rem;">
    <div class="card-title" style="margin-bottom:1rem;">{{ __('messages.store_types') }}</div>
    <form method="POST" action="{{ route('stores.types.store') }}" style="display:flex;gap:0.5rem;margin-bottom:1rem;">
        @csrf
        <input type="text" name="store_type_description" placeholder="e.g. Jewelry Shop" required style="flex:1;padding:0.625rem 0.75rem;border:1px solid var(--color-border);border-radius:8px;font-family:var(--font-body);font-size:0.9rem;">
        <button type="submit" class="btn btn-primary" style="font-size:0.85rem;">{{ __('messages.add_type') }}</button>
    </form>
    <div style="display:flex;flex-wrap:wrap;gap:0.5rem;">
        @foreach($types as $type)
            <span class="badge badge-info" style="font-size:0.85rem;padding:0.375rem 0.75rem;">{{ $type->store_type_description }}</span>
        @endforeach
    </div>
</div>
@endsection
