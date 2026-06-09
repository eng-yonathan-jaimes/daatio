@extends('tenant.layout')

@section('tab-content')
<div class="card" style="margin-bottom:1.5rem;max-width:560px;">
    <div class="card-title" style="margin-bottom:1rem;">{{ __('messages.change_password') }}</div>
    @if($errors->has('current_password') || $errors->has('new_password'))
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('account.security.password') }}">
        @csrf
        <div class="form-group">
            <label for="current_password">{{ __('messages.current_password_label') }}</label>
            <input type="password" id="current_password" name="current_password" required>
        </div>
        <div class="form-group">
            <label for="new_password">{{ __('messages.new_password') }}</label>
            <input type="password" id="new_password" name="new_password" required minlength="8">
        </div>
        <div class="form-group">
            <label for="new_password_confirmation">{{ __('messages.confirm_new_password') }}</label>
            <input type="password" id="new_password_confirmation" name="new_password_confirmation" required>
        </div>
        <button type="submit" class="btn btn-primary">{{ __('messages.update_password') }}</button>
    </form>
</div>

<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title" style="margin-bottom:1rem;">{{ __('messages.login_history') }}</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.ip_address') }}</th>
                    <th>{{ __('messages.device') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loginHistory as $log)
                    <tr>
                        <td>{{ $log->user_login_history_login_date->format('M d, Y h:i A') }}</td>
                        <td style="font-family:var(--font-mono);font-size:0.85rem;">{{ $log->user_login_history_ip }}</td>
                        <td style="font-size:0.8rem;max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $log->user_login_history_device }}">{{ $log->user_login_history_device }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align:center;color:var(--color-text-muted);padding:2rem;">{{ __('messages.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-title" style="margin-bottom:1rem;">{{ __('messages.recovery_history') }}</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.method') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.ip_address') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recoveryHistory as $log)
                    <tr>
                        <td>{{ $log->user_recovery_history_intent_date->format('M d, Y h:i A') }}</td>
                        <td>{{ $log->user_recovery_history_method_used }}</td>
                        <td>
                            <span class="badge badge-{{ $log->user_recovery_history_recovered_success ? 'success' : 'error' }}">
                                {{ $log->user_recovery_history_recovered_success ? __('messages.success') : __('messages.failed') }}
                            </span>
                        </td>
                        <td style="font-family:var(--font-mono);font-size:0.85rem;">{{ $log->user_recovery_history_ip }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--color-text-muted);padding:2rem;">{{ __('messages.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
