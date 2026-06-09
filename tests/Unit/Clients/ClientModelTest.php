<?php

namespace Tests\Unit\Clients;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Clients\app\Models\Client;
use Tests\TestCase;

class ClientModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_table_name_is_client(): void
    {
        $client = new Client();
        $this->assertSame('client', $client->getTable());
    }

    public function test_timestamps_are_disabled(): void
    {
        $client = new Client();
        $this->assertFalse($client->timestamps);
    }

    public function test_guarded_is_empty(): void
    {
        $client = new Client();
        $this->assertSame([], $client->getGuarded());
    }

    public function test_casts_correct_attributes(): void
    {
        $client = new Client();
        $this->assertSame('boolean', $client->getCasts()['client_active']);
        $this->assertSame('datetime', $client->getCasts()['client_registration_date']);
        $this->assertSame('datetime', $client->getCasts()['client_update_date']);
        $this->assertSame('array', $client->getCasts()['client_stores_ids']);
    }

    public function test_states_relationship(): void
    {
        $client = new Client();
        $relation = $client->states();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame('client_state_client_id', $relation->getForeignKeyName());
    }

    public function test_orders_relationship(): void
    {
        $client = new Client();
        $relation = $client->orders();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame('client_order_client_id', $relation->getForeignKeyName());
    }

    public function test_list_orders_relationship(): void
    {
        $client = new Client();
        $relation = $client->listOrders();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame('client_list_order_client_id', $relation->getForeignKeyName());
    }

    public function test_transactions_relationship(): void
    {
        $client = new Client();
        $relation = $client->transactions();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame('transaction_client_id', $relation->getForeignKeyName());
    }
}
