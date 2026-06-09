@extends('layouts.dashboard')

@section('title', $store->store_name)

@section('main')
<div class="page-header">
    <a href="{{ route('stores.index') }}" class="back-link">← {{ __('messages.stores') }}</a>
    <h1>{{ $store->store_name }}</h1>
</div>

@if($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
@endif

<div class="card" style="max-width:560px;">
    <form method="POST" action="{{ route('stores.update', $store->id) }}">
        @csrf
        <div class="form-group">
            <label for="store_name">{{ __('messages.store_name') }} <span style="color:#DC2626;">*</span></label>
            <input type="text" id="store_name" name="store_name" value="{{ old('store_name', $store->store_name) }}" required autofocus>
        </div>
        <div class="form-group">
            <label for="store_address">{{ __('messages.address') }}</label>
            <input type="text" id="store_address" name="store_address" value="{{ old('store_address', $store->store_address) }}" placeholder="123 Main St">
        </div>
        <div class="form-group">
            <label for="store_type_id">{{ __('messages.store_type') }} <span style="color:#DC2626;">*</span></label>
            <select id="store_type_id" name="store_type_id" style="width:100%;padding:0.75rem 1rem;border:1px solid var(--color-border);border-radius:8px;font-size:1rem;font-family:var(--font-body);color:var(--color-text);background:var(--color-card);">
                @foreach($types as $type)
                    <option value="{{ $type->id }}" {{ old('store_type_id', $store->store_type_id) == $type->id ? 'selected' : '' }}>{{ $type->store_type_description }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="store_location">{{ __('messages.location_type') }} <span style="color:#DC2626;">*</span></label>
            <select id="store_location" name="store_location" style="width:100%;padding:0.75rem 1rem;border:1px solid var(--color-border);border-radius:8px;font-size:1rem;font-family:var(--font-body);color:var(--color-text);background:var(--color-card);">
                <option value="Physical" {{ old('store_location', $store->store_location) == 'Physical' ? 'selected' : '' }}>{{ __('messages.physical') }}</option>
                <option value="Online" {{ old('store_location', $store->store_location) == 'Online' ? 'selected' : '' }}>{{ __('messages.online') }}</option>
                <option value="Both" {{ old('store_location', $store->store_location) == 'Both' ? 'selected' : '' }}>{{ __('messages.both') }}</option>
            </select>
        </div>
        <div style="display:flex;gap:1rem;margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
            <a href="{{ route('stores.index') }}" class="btn btn-outline">{{ __('messages.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
