<?php

namespace Modules\Clients\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Stores\app\Models\Store;

class ClientState extends Model
{
    protected $table = 'client_state';

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'client_state_amount' => 'decimal:4',
            'client_state_last_transaction_date' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_state_client_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'client_state_store_id');
    }
}
