<?php

namespace Modules\Stores\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Clients\app\Models\ClientListOrder;
use Modules\Clients\app\Models\ClientOrder;
use Modules\Clients\app\Models\ClientState;
use Modules\Products\app\Models\Product;
use Modules\Transactions\app\Models\Transaction;
use Modules\Users\app\Models\User;

class Store extends Model
{
    protected $table = 'store';

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'store_active' => 'boolean',
            'store_registration_date' => 'datetime',
            'store_update_date' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'store_user_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(StoreType::class, 'store_type_id');
    }

    public function clientStates(): HasMany
    {
        return $this->hasMany(ClientState::class, 'client_state_store_id');
    }

    public function clientOrders(): HasMany
    {
        return $this->hasMany(ClientOrder::class, 'client_order_store_id');
    }

    public function clientListOrders(): HasMany
    {
        return $this->hasMany(ClientListOrder::class, 'client_list_order_store_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'product_store_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'transaction_store_id');
    }
}
