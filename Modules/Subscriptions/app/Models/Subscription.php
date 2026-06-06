<?php

namespace Modules\Subscriptions\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    protected $table = 'subscription';

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'subscription_value' => 'decimal:4',
            'subscription_enabled' => 'boolean',
            'subscription_creation_date' => 'datetime',
        ];
    }

    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class, 'user_subscription_subscription_id');
    }
}
