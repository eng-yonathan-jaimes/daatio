<?php

namespace Tests\Unit\Clients;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Clients\app\Models\ClientOrder;
use Tests\TestCase;

class ClientOrderModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_table_name_is_client_order(): void
    {
        $order = new ClientOrder();
        $this->assertSame('client_order', $order->getTable());
    }

    public function test_timestamps_are_disabled(): void
    {
        $order = new ClientOrder();
        $this->assertFalse($order->timestamps);
    }

    public function test_guarded_is_empty(): void
    {
        $order = new ClientOrder();
        $this->assertSame([], $order->getGuarded());
    }

    public function test_casts_correct_attributes(): void
    {
        $order = new ClientOrder();
        $this->assertSame('decimal:4', $order->getCasts()['client_order_value']);
        $this->assertSame('datetime', $order->getCasts()['client_order_registration_date']);
        $this->assertSame('datetime', $order->getCasts()['client_order_date_due']);
    }

    public function test_client_relationship(): void
    {
        $order = new ClientOrder();
        $relation = $order->client();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('client_order_client_id', $relation->getForeignKeyName());
    }

    public function test_store_relationship(): void
    {
        $order = new ClientOrder();
        $relation = $order->store();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('client_order_store_id', $relation->getForeignKeyName());
    }

    public function test_items_relationship(): void
    {
        $order = new ClientOrder();
        $relation = $order->items();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame('client_list_order_id', $relation->getForeignKeyName());
    }

    public function test_transactions_relationship(): void
    {
        $order = new ClientOrder();
        $relation = $order->transactions();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame('transaction_client_order_id', $relation->getForeignKeyName());
    }
}
