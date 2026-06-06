<?php

namespace Modules\Users\app\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;

    protected $table = 'user';

    public $timestamps = false;

    protected $guarded = [];

    protected $hidden = [
        'user_password',
        'remember_token',
        'email_verification_code',
        'phone_verification_code',
    ];

    protected function casts(): array
    {
        return [
            'user_creation' => 'datetime',
            'user_update_date' => 'datetime',
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
        ];
    }

    public function getAuthPassword(): string
    {
        return $this->user_password;
    }

    public function getEmailAttribute()
    {
        return $this->user_email;
    }

    public function getNameAttribute()
    {
        return trim($this->user_name . ' ' . $this->user_lastName);
    }

    public function getPhoneAttribute()
    {
        return $this->user_phone_number;
    }

    public function hasVerifiedEmail(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function hasVerifiedPhone(): bool
    {
        return $this->phone_verified_at !== null;
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => now(),
        ])->save();
    }

    public function markPhoneAsVerified(): bool
    {
        return $this->forceFill([
            'phone_verified_at' => now(),
        ])->save();
    }

    public function generateEmailVerificationCode(): void
    {
        $this->forceFill([
            'email_verification_code' => Str::random(6),
        ])->save();
    }

    public function generatePhoneVerificationCode(): void
    {
        $this->forceFill([
            'phone_verification_code' => Str::random(6),
        ])->save();
    }

    public function sendEmailVerificationNotification(): void
    {
        Mail::raw(
            "Your Daatio verification code is: {$this->email_verification_code}",
            function ($message) {
                $message->to($this->user_email)
                    ->subject('Your verification code');
            }
        );
    }

    public function sendPhoneVerificationSms(): void
    {
        Mail::raw(
            "Your Daatio verification code is: {$this->phone_verification_code}",
            function ($message) {
                $message->to($this->user_phone_number . '@sms.local')
                    ->subject('Your verification code');
            }
        );
    }

    public function loginHistories(): HasMany
    {
        return $this->hasMany(UserLoginHistory::class, 'user_login_history_user_id');
    }

    public function recoveryHistories(): HasMany
    {
        return $this->hasMany(UserRecoveryHistory::class, 'user_recovery_history_user_id');
    }

    public function stores(): HasMany
    {
        return $this->hasMany(Store::class, 'store_user_id');
    }
}
