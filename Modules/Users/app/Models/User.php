<?php

namespace Modules\Users\app\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Stores\app\Models\Store;

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
    ];

    protected function casts(): array
    {
        return [
            'user_creation' => 'datetime',
            'user_update_date' => 'datetime',
        ];
    }

    public function getAuthPassword(): string
    {
        return $this->user_password;
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
