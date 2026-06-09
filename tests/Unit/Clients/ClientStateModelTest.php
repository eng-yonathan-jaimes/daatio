<?php

namespace Tests\Unit\Clients;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Clients\app\Models\ClientState;
use Tests\TestCase;

class ClientStateModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_table_name_is_client_state(): void
    {
        $state = new ClientState();
        $this->assertSame('client_state', $state->getTable());
    }

    public function test_timestamps_are_disabled(): void
    {
        $state = new ClientState();
        $this->assertFalse($state->timestamps);
    }

    public function test_guarded_is_empty(): void
    {
        $state = new ClientState();
        $this->assertSame([], $state->getGuarded());
    }

    public function test_casts_correct_attributes(): void
    {
        $state = new ClientState();
        $this->assertSame('decimal:4', $state->getCasts()['client_state_amount']);
        $this->assertSame('datetime', $state->getCasts()['client_state_last_transaction_date']);
    }

    public function test_client_relationship(): void
    {
        $state = new ClientState();
        $relation = $state->client();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('client_state_client_id', $relation->getForeignKeyName());
    }

    public function test_store_relationship(): void
    {
        $state = new ClientState();
        $relation = $state->store();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('client_state_store_id', $relation->getForeignKeyName());
    }
}
