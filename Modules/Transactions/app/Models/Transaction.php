<?php

namespace Modules\Transactions\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Clients\app\Models\Client;
use Modules\Clients\app\Models\ClientOrder;
use Modules\Stores\app\Models\Store;
use Modules\Users\app\Models\User;

class Transaction extends Model
{
    protected $table = 'transaction';

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'transaction_amount' => 'decimal:4',
            'transaction_registration_date' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'transaction_client_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'transaction_store_id');
    }

    public function clientOrder(): BelongsTo
    {
        return $this->belongsTo(ClientOrder::class, 'transaction_client_order_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transaction_user_id');
    }
}
