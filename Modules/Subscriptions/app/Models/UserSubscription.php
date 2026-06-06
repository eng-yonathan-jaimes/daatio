<?php

namespace Modules\Subscriptions\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Users\app\Models\User;

class UserSubscription extends Model
{
    protected $table = 'user_subscription';

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'user_subscription_value' => 'decimal:4',
            'user_subscription_start_date' => 'datetime',
            'user_subscription_end_date' => 'datetime',
            'user_subscription_trial_ends_date' => 'datetime',
            'user_subscription_cancelled_date' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_subscription_user_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'user_subscription_subscription_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class, 'subscription_payment_user_subscription_id');
    }

    public function isActive(): bool
    {
        return $this->user_subscription_status === 'Active'
            && $this->user_subscription_end_date->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->user_subscription_end_date->isPast()
            || $this->user_subscription_status === 'Expired';
    }

    public function daysRemaining(): int
    {
        return max(0, (int) now()->diffInDays($this->user_subscription_end_date, false));
    }
}
