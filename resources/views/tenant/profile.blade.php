@extends('tenant.layout')

@section('tab-content')
<div style="max-width:560px;">
    <form method="POST" action="{{ route('account.profile.update') }}">
        @csrf

        <div style="display:flex;gap:0.75rem;">
            <div class="form-group" style="flex:1;">
                <label for="user_name">{{ __('messages.first_name') }}</label>
                <input type="text" id="user_name" name="user_name" value="{{ old('user_name', $user->user_name) }}" required>
            </div>
            <div class="form-group" style="flex:1;">
                <label for="user_lastName">{{ __('messages.last_name') }}</label>
                <input type="text" id="user_lastName" name="user_lastName" value="{{ old('user_lastName', $user->user_lastName) }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="user_email">{{ __('messages.email_address') }}</label>
            <input type="email" id="user_email" name="user_email" value="{{ old('user_email', $user->user_email) }}" required>
        </div>

        <div class="form-group">
            <label for="user_phone_number">{{ __('messages.phone_number') }}</label>
            <input type="text" id="user_phone_number" name="user_phone_number" value="{{ old('user_phone_number', $user->user_phone_number) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
    </form>
</div>
@endsection
