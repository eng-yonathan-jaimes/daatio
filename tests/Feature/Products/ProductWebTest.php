<?php

namespace Tests\Feature\Products;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Products\app\Models\Product;
use Modules\Stores\app\Models\Store;
use Modules\Stores\app\Models\StoreType;
use Modules\Users\app\Models\User;
use Tests\TestCase;

class ProductWebTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::create([
            'tenant_id' => 1,
            'user_name' => 'John',
            'user_lastName' => 'Doe',
            'user_email' => 'john.doe@example.com',
            'user_access' => 'owner',
            'user_password' => bcrypt('password123'),
            'user_phone_number' => '+123456789',
            'user_update_date' => now(),
        ]);
    }

    private function createStore($userId): Store
    {
        $type = StoreType::create(['store_type_description' => 'General Shop']);
        return Store::create([
            'store_user_id' => $userId,
            'store_name' => 'Store 1',
            'store_address' => '123 Street',
            'store_type_id' => $type->id,
            'store_location' => 'Physical',
            'store_active' => true,
            'store_registration_date' => now(),
            'store_update_date' => now(),
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('products.index'))->assertRedirect(route('login'));
        $this->get(route('products.create'))->assertRedirect(route('login'));
        $this->post(route('products.store'), [])->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_products_index(): void
    {
        $user = $this->createUser();
        $store = $this->createStore($user->id);

        Product::create([
            'product_store_id' => $store->id,
            'product_name' => 'Gold 24K',
            'product_type' => 0,
            'product_weight' => 10.5000,
            'product_value' => 500.0000,
            'product_currency' => 'USD',
            'product_state' => 'In Stock',
            'product_registration_date' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('products.index'));

        $response->assertOk()
            ->assertViewIs('products.index')
            ->assertViewHas('products')
            ->assertSee('Gold 24K');
    }

    public function test_authenticated_user_can_create_product(): void
    {
        $user = $this->createUser();
        $store = $this->createStore($user->id);

        $response = $this->actingAs($user)->post(route('products.store'), [
            'product_name' => 'Silver 925',
            'product_weight' => 50.00,
            'product_value' => 100.00,
            'product_currency' => 'USD',
        ]);

        $response->assertRedirect(route('products.index'))
            ->assertSessionHas('status', 'Product created.');

        $this->assertDatabaseHas('product', [
            'product_store_id' => $store->id,
            'product_name' => 'Silver 925',
            'product_weight' => 50.00,
            'product_value' => 100.00,
            'product_currency' => 'USD',
            'product_state' => 'In Stock',
        ]);
    }

    public function test_create_product_validation(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post(route('products.store'), []);

        $response->assertSessionHasErrors(['product_name']);
    }

    public function test_authenticated_user_can_edit_and_update_product(): void
    {
        $user = $this->createUser();
        $store = $this->createStore($user->id);

        $product = Product::create([
            'product_store_id' => $store->id,
            'product_name' => 'Gold 24K',
            'product_type' => 0,
            'product_weight' => 10.5000,
            'product_value' => 500.0000,
            'product_currency' => 'USD',
            'product_state' => 'In Stock',
            'product_registration_date' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('products.edit', $product->id));
        $response->assertOk()->assertViewIs('products.edit');

        $response = $this->actingAs($user)->post(route('products.update', $product->id), [
            'product_name' => 'Gold 24K Updated',
            'product_weight' => 12.00,
            'product_value' => 600.00,
            'product_currency' => 'EUR',
            'product_state' => 'Out of Stock',
        ]);

        $response->assertRedirect(route('products.index'))
            ->assertSessionHas('status', 'Product updated.');

        $this->assertDatabaseHas('product', [
            'id' => $product->id,
            'product_name' => 'Gold 24K Updated',
            'product_weight' => 12.00,
            'product_value' => 600.00,
            'product_currency' => 'EUR',
            'product_state' => 'Out of Stock',
        ]);
    }

    public function test_authenticated_user_can_delete_product(): void
    {
        $user = $this->createUser();
        $store = $this->createStore($user->id);

        $product = Product::create([
            'product_store_id' => $store->id,
            'product_name' => 'Gold 24K',
            'product_type' => 0,
            'product_weight' => 10.5000,
            'product_value' => 500.0000,
            'product_currency' => 'USD',
            'product_state' => 'In Stock',
            'product_registration_date' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('products.destroy', $product->id));

        $response->assertRedirect(route('products.index'))
            ->assertSessionHas('status', 'Product deleted.');

        $this->assertDatabaseMissing('product', [
            'id' => $product->id,
        ]);
    }
}
