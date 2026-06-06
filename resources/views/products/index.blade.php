@extends('layouts.dashboard')

@section('title', 'Products')

@section('main')
<div class="page-header">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h1>Products</h1>
            <p>Manage metal types for your store</p>
        </div>
        <a href="{{ route('products.create') }}" class="btn btn-primary">+ New Product</a>
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
                    <th>Name</th>
                    <th>Weight</th>
                    <th>Value</th>
                    <th>State</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td style="font-weight:500;">{{ $product->product_name }}</td>
                        <td>{{ $product->product_weight > 0 ? rtrim(rtrim(number_format($product->product_weight, 4), '0'), '.') . 'g' : '-' }}</td>
                        <td>{{ $product->product_value > 0 ? '$' . number_format($product->product_value, 2) . ' ' . ($product->product_currency ?? 'USD') : '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $product->product_state === 'In Stock' ? 'success' : 'warning' }}">
                                {{ $product->product_state }}
                            </span>
                        </td>
                        <td>{{ $product->product_registration_date->format('M d, Y') }}</td>
                        <td style="text-align:right;">
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-outline" style="padding:0.375rem 0.75rem;font-size:0.8rem;">Edit</a>
                            <form method="POST" action="{{ route('products.destroy', $product->id) }}" style="display:inline;" onsubmit="return confirm('Delete this product?')">
                                @csrf
                                <button type="submit" class="btn btn-outline" style="padding:0.375rem 0.75rem;font-size:0.8rem;color:#DC2626;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;color:var(--color-text-muted);padding:2rem;">
                            No products yet. <a href="{{ route('products.create') }}">Add your first metal type</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
