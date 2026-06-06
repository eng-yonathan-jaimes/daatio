@extends('layouts.dashboard')

@section('title', 'Edit ' . $product->product_name)

@section('main')
<div class="page-header">
    <a href="{{ route('products.index') }}" class="back-link">← Back to products</a>
    <h1>Edit {{ $product->product_name }}</h1>
</div>

@if($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
@endif

<div class="card" style="max-width:560px;">
    <form method="POST" action="{{ route('products.update', $product->id) }}">
        @csrf
        <div class="form-group">
            <label for="product_name">Product Name <span style="color:#DC2626;">*</span></label>
            <input type="text" id="product_name" name="product_name" value="{{ old('product_name', $product->product_name) }}" required autofocus>
        </div>

        <div class="form-group">
            <label for="product_weight">Weight</label>
            <input type="number" step="0.0001" id="product_weight" name="product_weight" value="{{ old('product_weight', $product->product_weight) }}" placeholder="0.0000">
        </div>

        <div class="form-group">
            <label for="product_value">Value</label>
            <div style="display:flex;gap:0.5rem;">
                <input type="number" step="0.0001" id="product_value" name="product_value" value="{{ old('product_value', $product->product_value) }}" placeholder="0.0000" style="flex:1;">
                <select name="product_currency" class="country-select" style="width:120px;">
                    @foreach(['USD', 'COP', 'EUR', 'VES', 'MXN', 'PEN', 'ARS', 'CLP', 'BRL'] as $cur)
                        <option value="{{ $cur }}" {{ old('product_currency', $product->product_currency ?? 'USD') === $cur ? 'selected' : '' }}>{{ $cur }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="product_state">State</label>
            <select id="product_state" name="product_state" style="width:100%;padding:0.75rem 1rem;border:1px solid var(--color-border);border-radius:8px;font-size:1rem;font-family:var(--font-body);color:var(--color-text);background:var(--color-card);">
                <option value="In Stock" {{ old('product_state', $product->product_state) === 'In Stock' ? 'selected' : '' }}>In Stock</option>
                <option value="Out of Stock" {{ old('product_state', $product->product_state) === 'Out of Stock' ? 'selected' : '' }}>Out of Stock</option>
            </select>
        </div>

        <div style="display:flex;gap:1rem;margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('products.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
