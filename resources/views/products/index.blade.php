@extends('layouts.dashboard')

@section('title', __('messages.products'))

@section('main')
<div class="page-header">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h1>{{ __('messages.products') }}</h1>
            <p>{{ __('messages.manage_products') }}</p>
        </div>
        <a href="{{ route('products.create') }}" class="btn btn-primary">{{ __('messages.new_product') }}</a>
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
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.metal_weight') }}</th>
                    <th>{{ __('messages.initial_value') }}</th>
                    <th>{{ __('messages.product_state') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td style="font-weight:500;">{{ $product->product_name }}</td>
                        <td>{{ $product->product_weight > 0 ? rtrim(rtrim(number_format($product->product_weight, 4), '0'), '.') . 'g' : __('messages.none') }}</td>
                        <td>{{ $product->product_value > 0 ? '$' . number_format($product->product_value, 2) . ' ' . ($product->product_currency ?? 'USD') : __('messages.none') }}</td>
                        <td>
                            <span class="badge badge-{{ $product->product_state === 'In Stock' ? 'success' : 'warning' }}">
                                {{ $product->product_state === 'In Stock' ? __('messages.in_stock') : __('messages.out_of_stock') }}
                            </span>
                        </td>
                        <td style="text-align:right;">
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-outline" style="padding:0.375rem 0.75rem;font-size:0.8rem;">{{ __('messages.edit') }}</a>
                            <form method="POST" action="{{ route('products.destroy', $product->id) }}" style="display:inline;" onsubmit="return confirm('{{ __('messages.delete_confirm') }}')">
                                @csrf
                                <button type="submit" class="btn btn-outline" style="padding:0.375rem 0.75rem;font-size:0.8rem;color:#DC2626;">{{ __('messages.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;color:var(--color-text-muted);padding:2rem;">
                            {{ __('messages.add_first_product') }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
