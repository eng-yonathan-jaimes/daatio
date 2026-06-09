<?php

namespace Tests\Unit\Stores;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Stores\app\Models\StoreType;
use Tests\TestCase;

class StoreTypeModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_table_name_is_store_type(): void
    {
        $type = new StoreType();
        $this->assertSame('store_type', $type->getTable());
    }

    public function test_timestamps_are_disabled(): void
    {
        $type = new StoreType();
        $this->assertFalse($type->timestamps);
    }

    public function test_guarded_is_empty(): void
    {
        $type = new StoreType();
        $this->assertSame([], $type->getGuarded());
    }

    public function test_stores_relationship(): void
    {
        $type = new StoreType();
        $relation = $type->stores();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame('store_type_id', $relation->getForeignKeyName());
    }
}
