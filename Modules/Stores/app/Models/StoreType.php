<?php

namespace Modules\Stores\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreType extends Model
{
    protected $table = 'store_type';

    public $timestamps = false;

    protected $guarded = [];

    public function stores(): HasMany
    {
        return $this->hasMany(Store::class, 'store_type_id');
    }
}
