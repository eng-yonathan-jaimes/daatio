<?php

namespace Modules\Clients\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Transactions\app\Models\Transaction;

class Client extends Model
{
    protected $table = 'client';

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'client_active' => 'boolean',
            'client_registration_date' => 'datetime',
            'client_update_date' => 'datetime',
            'client_stores_ids' => 'array',
        ];
    }

    public function states(): HasMany
    {
        return $this->hasMany(ClientState::class, 'client_state_client_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(ClientOrder::class, 'client_order_client_id');
    }

    public function listOrders(): HasMany
    {
        return $this->hasMany(ClientListOrder::class, 'client_list_order_client_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'transaction_client_id');
    }
}
