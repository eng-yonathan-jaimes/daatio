@extends('layouts.dashboard')

@php
$labels = [
    'Buying' => 'Add Debt',
    'Paying' => 'Register Payment',
    'Selling' => 'Metal Purchase',
    'Retriving' => 'Deliver Money',
    'Settle' => 'Settle Account',
];
$title = $labels[$type] ?? 'Transaction';
@endphp

@section('title', $title . ' - ' . $client->client_name)

@section('main')
<div class="page-header">
    <a href="{{ route('customers.show', $client->id) }}" class="back-link">← Back to {{ $client->client_name }}</a>
    <h1>{{ $title }}</h1>
    <p>
        Current balance:
        <strong style="color:{{ ($state->client_state_amount ?? 0) > 0 ? 'var(--color-primary)' : (($state->client_state_amount ?? 0) < 0 ? '#DC2626' : 'var(--color-text)') }};">
            ${{ number_format(abs($state->client_state_amount ?? 0), 2) }}
        </strong>
        <span class="badge badge-{{ ($state->client_state_amount ?? 0) > 0 ? 'info' : (($state->client_state_amount ?? 0) < 0 ? 'error' : 'success') }}">
            {{ ($state->client_state_amount ?? 0) > 0 ? 'Favor' : (($state->client_state_amount ?? 0) < 0 ? 'Debit' : 'Settled') }}
        </span>
    </p>
</div>

@if($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
@endif

<div class="card" style="max-width:560px;">
    <form method="POST" action="{{ route('transactions.store', $client->id) }}" onsubmit="return confirm('Record this {{ strtolower($title) }}?')">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">

        @if($type === 'Buying')
            <div class="form-group">
                <label for="amount">Debt Amount <span style="color:#DC2626;">*</span></label>
                <input type="number" step="0.0001" id="amount" name="amount" value="{{ old('amount') }}" placeholder="0.0000" required autofocus>
            </div>
            <div class="form-group">
                <label for="description">Description / Concept</label>
                <input type="text" id="description" name="description" value="{{ old('description') }}" placeholder="e.g. Metal advance">
            </div>
            <div class="card-title" style="margin-bottom:1rem;margin-top:1rem;">Line Items (optional)</div>
            <div class="form-group">
                <label for="product_id">Product / Metal</label>
                <select id="product_id" name="product_id" style="width:100%;padding:0.75rem 1rem;border:1px solid var(--color-border);border-radius:8px;font-size:1rem;font-family:var(--font-body);color:var(--color-text);background:var(--color-card);">
                    <option value="">— None —</option>
                    @foreach(\Modules\Products\app\Models\Product::orderBy('product_name')->get() as $p)
                        <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->product_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="item_weight">Weight</label>
                <input type="number" step="0.0001" id="item_weight" name="item_weight" value="{{ old('item_weight') }}" placeholder="0.0000">
            </div>

        @elseif($type === 'Paying')
            <div class="form-group">
                <label for="amount">Payment Amount <span style="color:#DC2626;">*</span></label>
                <input type="number" step="0.0001" id="amount" name="amount" value="{{ old('amount') }}" placeholder="0.0000" required autofocus>
                <div style="font-size:0.8rem;color:var(--color-text-muted);margin-top:0.25rem;">Current debt: ${{ number_format(abs($state->client_state_amount ?? 0), 2) }}</div>
            </div>
            <div class="form-group">
                <label for="note">Note</label>
                <input type="text" id="note" name="note" value="{{ old('note') }}" placeholder="e.g. Partial payment">
            </div>

        @elseif($type === 'Selling')
            <div class="form-group">
                <label for="amount">Total Amount Paid <span style="color:#DC2626;">*</span></label>
                <input type="number" step="0.0001" id="amount" name="amount" value="{{ old('amount') }}" placeholder="0.0000" required autofocus>
            </div>
            <div class="card-title" style="margin-bottom:1rem;margin-top:1rem;">Line Items (optional)</div>
            <div class="form-group">
                <label for="product_id">Product / Metal</label>
                <select id="product_id" name="product_id" style="width:100%;padding:0.75rem 1rem;border:1px solid var(--color-border);border-radius:8px;font-size:1rem;font-family:var(--font-body);color:var(--color-text);background:var(--color-card);">
                    <option value="">— None —</option>
                    @foreach(\Modules\Products\app\Models\Product::orderBy('product_name')->get() as $p)
                        <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->product_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="item_weight">Weight</label>
                <input type="number" step="0.0001" id="item_weight" name="item_weight" value="{{ old('item_weight') }}" placeholder="0.0000">
            </div>
            <div class="form-group">
                <label for="note">Note</label>
                <input type="text" id="note" name="note" value="{{ old('note') }}" placeholder="e.g. Metal purchase">
            </div>

        @elseif($type === 'Retriving')
            <div class="form-group">
                <label for="amount">Amount Delivered <span style="color:#DC2626;">*</span></label>
                <input type="number" step="0.0001" id="amount" name="amount" value="{{ old('amount') }}" placeholder="0.0000" required autofocus>
                <div style="font-size:0.8rem;color:var(--color-text-muted);margin-top:0.25rem;">Available: ${{ number_format($state->client_state_amount ?? 0, 2) }}</div>
            </div>
            <div class="form-group">
                <label for="recipient_name">Recipient Name <span style="color:#DC2626;">*</span></label>
                <input type="text" id="recipient_name" name="recipient_name" value="{{ old('recipient_name', $client->client_name . ' ' . $client->client_last_name) }}" required>
            </div>
            <div class="form-group">
                <label for="note">Note</label>
                <input type="text" id="note" name="note" value="{{ old('note') }}" placeholder="e.g. Cash withdrawal">
            </div>

        @elseif($type === 'Settle')
            <p style="margin-bottom:1.5rem;color:var(--color-text-muted);">
                This will settle the customer's account.
                @if(($state->client_state_amount ?? 0) != 0)
                    The current balance of
                    <strong>${{ number_format(abs($state->client_state_amount), 2) }}</strong>
                    ({{ ($state->client_state_amount ?? 0) > 0 ? 'Favor' : 'Debit' }}) will be cleared.
                @else
                    The account is already at zero.
                @endif
            </p>
        @endif

        <div style="display:flex;gap:1rem;margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary"
                @if($type === 'Settle' && ($state->client_state_amount ?? 0) == 0) disabled @endif>
                {{ $type === 'Settle' ? 'Confirm Settlement' : 'Save' }}
            </button>
            <a href="{{ route('customers.show', $client->id) }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
