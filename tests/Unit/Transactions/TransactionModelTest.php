<?php

namespace Tests\Unit\Transactions;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Transactions\app\Models\Transaction;
use Tests\TestCase;

class TransactionModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_table_name_is_transaction(): void
    {
        $transaction = new Transaction();
        $this->assertSame('transaction', $transaction->getTable());
    }

    public function test_timestamps_are_disabled(): void
    {
        $transaction = new Transaction();
        $this->assertFalse($transaction->timestamps);
    }

    public function test_guarded_is_empty(): void
    {
        $transaction = new Transaction();
        $this->assertSame([], $transaction->getGuarded());
    }

    public function test_casts_correct_attributes(): void
    {
        $transaction = new Transaction();
        $this->assertSame('decimal:4', $transaction->getCasts()['transaction_amount']);
        $this->assertSame('datetime', $transaction->getCasts()['transaction_registration_date']);
    }

    public function test_client_relationship(): void
    {
        $transaction = new Transaction();
        $relation = $transaction->client();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('transaction_client_id', $relation->getForeignKeyName());
    }

    public function test_store_relationship(): void
    {
        $transaction = new Transaction();
        $relation = $transaction->store();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('transaction_store_id', $relation->getForeignKeyName());
    }

    public function test_client_order_relationship(): void
    {
        $transaction = new Transaction();
        $relation = $transaction->clientOrder();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('transaction_client_order_id', $relation->getForeignKeyName());
    }

    public function test_user_relationship(): void
    {
        $transaction = new Transaction();
        $relation = $transaction->user();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('transaction_user_id', $relation->getForeignKeyName());
    }
}
