<?php

namespace Modules\Products\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Clients\app\Models\ClientListOrder;
use Modules\Stores\app\Models\Store;

class Product extends Model
{
    protected $table = 'product';

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'product_weight' => 'decimal:4',
            'product_value' => 'decimal:4',
            'product_registration_date' => 'datetime',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'product_store_id');
    }

    public function clientListOrders(): HasMany
    {
        return $this->hasMany(ClientListOrder::class, 'client_list_order_product_id');
    }
}
