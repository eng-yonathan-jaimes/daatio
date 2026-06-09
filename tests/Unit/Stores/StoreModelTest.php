<?php

namespace Tests\Unit\Stores;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Stores\app\Models\Store;
use Modules\Stores\app\Models\StoreType;
use Modules\Users\app\Models\User;
use Tests\TestCase;

class StoreModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_table_name_is_store(): void
    {
        $store = new Store();
        $this->assertSame('store', $store->getTable());
    }

    public function test_timestamps_are_disabled(): void
    {
        $store = new Store();
        $this->assertFalse($store->timestamps);
    }

    public function test_guarded_is_empty(): void
    {
        $store = new Store();
        $this->assertSame([], $store->getGuarded());
    }

    public function test_casts_correct_attributes(): void
    {
        $store = new Store();
        $this->assertSame('boolean', $store->getCasts()['store_active']);
        $this->assertSame('datetime', $store->getCasts()['store_registration_date']);
        $this->assertSame('datetime', $store->getCasts()['store_update_date']);
    }

    public function test_user_relationship(): void
    {
        $store = new Store();
        $relation = $store->user();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('store_user_id', $relation->getForeignKeyName());
    }

    public function test_type_relationship(): void
    {
        $store = new Store();
        $relation = $store->type();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('store_type_id', $relation->getForeignKeyName());
    }

    public function test_client_states_relationship(): void
    {
        $store = new Store();
        $relation = $store->clientStates();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame('client_state_store_id', $relation->getForeignKeyName());
    }

    public function test_client_orders_relationship(): void
    {
        $store = new Store();
        $relation = $store->clientOrders();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame('client_order_store_id', $relation->getForeignKeyName());
    }

    public function test_client_list_orders_relationship(): void
    {
        $store = new Store();
        $relation = $store->clientListOrders();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame('client_list_order_store_id', $relation->getForeignKeyName());
    }

    public function test_products_relationship(): void
    {
        $store = new Store();
        $relation = $store->products();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame('product_store_id', $relation->getForeignKeyName());
    }

    public function test_transactions_relationship(): void
    {
        $store = new Store();
        $relation = $store->transactions();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame('transaction_store_id', $relation->getForeignKeyName());
    }
}
