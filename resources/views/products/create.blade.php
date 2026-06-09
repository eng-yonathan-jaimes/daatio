@extends('layouts.dashboard')

@section('title', __('messages.new_product'))

@section('main')
<div class="page-header">
    <a href="{{ route('products.index') }}" class="back-link">← {{ __('messages.products') }}</a>
    <h1>{{ __('messages.new_product') }}</h1>
    <p>{{ __('messages.add_first_product') }}</p>
</div>

@if($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
@endif

<div class="card" style="max-width:560px;">
    <form method="POST" action="{{ route('products.store') }}">
        @csrf
        <div class="form-group">
            <label for="product_name">{{ __('messages.product_name') }} <span style="color:#DC2626;">*</span></label>
            <input type="text" id="product_name" name="product_name" value="{{ old('product_name') }}" placeholder="e.g. Gold 18K" required autofocus>
        </div>
        <div class="form-group">
            <label for="product_weight">{{ __('messages.initial_weight') }}</label>
            <input type="number" step="0.0001" id="product_weight" name="product_weight" value="{{ old('product_weight') }}" placeholder="0.0000">
            <div style="font-size:0.8rem;color:var(--color-text-muted);margin-top:0.25rem;">{{ __('messages.stock_weight_hint') }}</div>
        </div>
        <div class="form-group">
            <label for="product_value">{{ __('messages.initial_value') }}</label>
            <div style="display:flex;gap:0.5rem;">
                <input type="number" step="0.0001" id="product_value" name="product_value" value="{{ old('product_value') }}" placeholder="0.0000" style="flex:1;">
                <select name="product_currency" class="country-select" style="width:120px;">
                    @foreach(['USD', 'COP', 'EUR', 'VES', 'MXN', 'PEN', 'ARS', 'CLP', 'BRL'] as $cur)
                        <option value="{{ $cur }}" {{ old('product_currency', 'USD') === $cur ? 'selected' : '' }}>{{ $cur }}</option>
                    @endforeach
                </select>
            </div>
            <div style="font-size:0.8rem;color:var(--color-text-muted);margin-top:0.25rem;">{{ __('messages.stock_value_hint') }}</div>
        </div>
        <div style="display:flex;gap:1rem;margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
            <a href="{{ route('products.index') }}" class="btn btn-outline">{{ __('messages.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
