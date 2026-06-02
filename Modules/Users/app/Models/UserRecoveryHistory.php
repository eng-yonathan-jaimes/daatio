<?php

namespace Modules\Users\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRecoveryHistory extends Model
{
    protected $table = 'user_recovery_history';

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'user_recovery_history_intent_date' => 'datetime',
            'user_recovery_history_recovered_success' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_recovery_history_user_id');
    }
}
