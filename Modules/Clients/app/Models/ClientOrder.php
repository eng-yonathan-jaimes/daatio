<?php

namespace Modules\Clients\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Stores\app\Models\Store;
use Modules\Transactions\app\Models\Transaction;

class ClientOrder extends Model
{
    protected $table = 'client_order';

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'client_order_value' => 'decimal:4',
            'client_order_registration_date' => 'datetime',
            'client_order_date_due' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_order_client_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'client_order_store_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ClientListOrder::class, 'client_list_order_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'transaction_client_order_id');
    }
}
