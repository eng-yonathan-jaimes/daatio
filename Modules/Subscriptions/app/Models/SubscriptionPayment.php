<?php

namespace Modules\Subscriptions\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
    protected $table = 'subscription_payment';

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'subscription_payment_amount' => 'decimal:4',
            'subscription_payment_date' => 'datetime',
            'subscription_payment_period_start' => 'datetime',
            'subscription_payment_period_end' => 'datetime',
        ];
    }

    public function userSubscription(): BelongsTo
    {
        return $this->belongsTo(UserSubscription::class, 'subscription_payment_user_subscription_id');
    }
}
