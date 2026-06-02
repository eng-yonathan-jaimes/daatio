<?php

namespace Modules\Clients\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Products\app\Models\Product;
use Modules\Stores\app\Models\Store;

class ClientListOrder extends Model
{
    protected $table = 'client_list_order';

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'client_list_order_weight' => 'decimal:4',
            'client_list_order_registration_date' => 'datetime',
            'client_list_order_value' => 'decimal:4',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_list_order_client_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'client_list_order_store_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(ClientOrder::class, 'client_list_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'client_list_order_product_id');
    }
}
