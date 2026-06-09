<?php

namespace Tests\Unit\Clients;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Clients\app\Models\ClientListOrder;
use Tests\TestCase;

class ClientListOrderModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_table_name_is_client_list_order(): void
    {
        $listOrder = new ClientListOrder();
        $this->assertSame('client_list_order', $listOrder->getTable());
    }

    public function test_timestamps_are_disabled(): void
    {
        $listOrder = new ClientListOrder();
        $this->assertFalse($listOrder->timestamps);
    }

    public function test_guarded_is_empty(): void
    {
        $listOrder = new ClientListOrder();
        $this->assertSame([], $listOrder->getGuarded());
    }

    public function test_casts_correct_attributes(): void
    {
        $listOrder = new ClientListOrder();
        $this->assertSame('decimal:4', $listOrder->getCasts()['client_list_order_weight']);
        $this->assertSame('datetime', $listOrder->getCasts()['client_list_order_registration_date']);
        $this->assertSame('decimal:4', $listOrder->getCasts()['client_list_order_value']);
    }

    public function test_client_relationship(): void
    {
        $listOrder = new ClientListOrder();
        $relation = $listOrder->client();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('client_list_order_client_id', $relation->getForeignKeyName());
    }

    public function test_store_relationship(): void
    {
        $listOrder = new ClientListOrder();
        $relation = $listOrder->store();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('client_list_order_store_id', $relation->getForeignKeyName());
    }

    public function test_order_relationship(): void
    {
        $listOrder = new ClientListOrder();
        $relation = $listOrder->order();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('client_list_order_id', $relation->getForeignKeyName());
    }

    public function test_product_relationship(): void
    {
        $listOrder = new ClientListOrder();
        $relation = $listOrder->product();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('client_list_order_product_id', $relation->getForeignKeyName());
    }
}
