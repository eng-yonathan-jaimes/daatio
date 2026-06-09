@extends('layouts.dashboard')

@section('title', __('messages.new_customer'))

@php
$countryCodes = [
    ['code' => '+1', 'flag' => '🇺🇸', 'name' => 'US/Canada +1'],
    ['code' => '+52', 'flag' => '🇲🇽', 'name' => 'Mexico +52'],
    ['code' => '+503', 'flag' => '🇸🇻', 'name' => 'El Salvador +503'],
    ['code' => '+504', 'flag' => '🇭🇳', 'name' => 'Honduras +504'],
    ['code' => '+505', 'flag' => '🇳🇮', 'name' => 'Nicaragua +505'],
    ['code' => '+506', 'flag' => '🇨🇷', 'name' => 'Costa Rica +506'],
    ['code' => '+507', 'flag' => '🇵🇦', 'name' => 'Panama +507'],
    ['code' => '+57', 'flag' => '🇨🇴', 'name' => 'Colombia +57'],
    ['code' => '+58', 'flag' => '🇻🇪', 'name' => 'Venezuela +58'],
    ['code' => '+51', 'flag' => '🇵🇪', 'name' => 'Peru +51'],
    ['code' => '+593', 'flag' => '🇪🇨', 'name' => 'Ecuador +593'],
    ['code' => '+54', 'flag' => '🇦🇷', 'name' => 'Argentina +54'],
    ['code' => '+56', 'flag' => '🇨🇱', 'name' => 'Chile +56'],
    ['code' => '+55', 'flag' => '🇧🇷', 'name' => 'Brazil +55'],
    ['code' => '+595', 'flag' => '🇵🇾', 'name' => 'Paraguay +595'],
    ['code' => '+598', 'flag' => '🇺🇾', 'name' => 'Uruguay +598'],
    ['code' => '+591', 'flag' => '🇧🇴', 'name' => 'Bolivia +591'],
    ['code' => '+34', 'flag' => '🇪🇸', 'name' => 'Spain +34'],
];
@endphp

@section('main')
<div class="page-header">
    <h1>{{ __('messages.new_customer') }}</h1>
    <p>{{ __('messages.register_customer') }}</p>
</div>

@if($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
@endif

<div class="card" style="max-width:560px;">
    <form method="POST" action="{{ route('customers.store') }}" onsubmit="return confirm('{{ __('messages.save_customer_confirm') }}')">
        @csrf

        <div style="display:flex;gap:0.75rem;">
            <div class="form-group" style="flex:1;">
                <label for="first_name">{{ __('messages.first_name') }} <span style="color:#DC2626;">*</span></label>
                <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="John" required autofocus>
            </div>
            <div class="form-group" style="flex:1;">
                <label for="last_name">{{ __('messages.last_name') }} <span style="color:#DC2626;">*</span></label>
                <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Doe" required>
            </div>
        </div>

        <div class="form-group">
            <label for="phone">{{ __('messages.phone_number') }} <span style="color:#DC2626;">*</span></label>
            <div class="phone-group">
                <select name="country_code" id="country_code" class="country-select">
                    @foreach($countryCodes as $c)
                        <option value="{{ $c['code'] }}" {{ old('country_code', '+57') == $c['code'] ? 'selected' : '' }}>{{ $c['flag'] }} {{ $c['code'] }}</option>
                    @endforeach
                </select>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="318 000 0000" required>
            </div>
        </div>

        <div class="form-group">
            <label for="email">{{ __('messages.email_address') }}</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="customer@example.com">
        </div>

        <div class="form-group">
            <label for="document_type">{{ __('messages.document') }}</label>
            <select id="document_type" name="document_type" class="country-select" style="width:100%;">
                <option value="Cedula" {{ old('document_type') == 'Cedula' ? 'selected' : '' }}>Cedula</option>
                <option value="Passport" {{ old('document_type') == 'Passport' ? 'selected' : '' }}>Passport</option>
                <option value="PPT" {{ old('document_type') == 'PPT' ? 'selected' : '' }}>PPT</option>
            </select>
        </div>

        <div class="form-group">
            <label for="document_number">{{ __('messages.document') }} #</label>
            <input type="text" id="document_number" name="document_number" value="{{ old('document_number') }}" placeholder="ID number">
        </div>

        <div style="display:flex;gap:1rem;margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary">{{ __('messages.save_customer') }}</button>
            <a href="{{ route('customers.index') }}" class="btn btn-outline" onclick="return confirm('{{ __('messages.discard_changes') }}')">{{ __('messages.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
