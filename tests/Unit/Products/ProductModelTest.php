<?php

namespace Tests\Unit\Products;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Products\app\Models\Product;
use Tests\TestCase;

class ProductModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_table_name_is_product(): void
    {
        $product = new Product();
        $this->assertSame('product', $product->getTable());
    }

    public function test_timestamps_are_disabled(): void
    {
        $product = new Product();
        $this->assertFalse($product->timestamps);
    }

    public function test_guarded_is_empty(): void
    {
        $product = new Product();
        $this->assertSame([], $product->getGuarded());
    }

    public function test_casts_correct_attributes(): void
    {
        $product = new Product();
        $this->assertSame('decimal:4', $product->getCasts()['product_weight']);
        $this->assertSame('decimal:4', $product->getCasts()['product_value']);
        $this->assertSame('datetime', $product->getCasts()['product_registration_date']);
    }

    public function test_store_relationship(): void
    {
        $product = new Product();
        $relation = $product->store();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame('product_store_id', $relation->getForeignKeyName());
    }

    public function test_client_list_orders_relationship(): void
    {
        $product = new Product();
        $relation = $product->clientListOrders();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame('client_list_order_product_id', $relation->getForeignKeyName());
    }
}
