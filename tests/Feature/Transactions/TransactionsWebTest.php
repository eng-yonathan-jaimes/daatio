<?php

namespace Tests\Feature\Transactions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Clients\app\Models\Client;
use Modules\Clients\app\Models\ClientOrder;
use Modules\Clients\app\Models\ClientState;
use Modules\Products\app\Models\Product;
use Modules\Stores\app\Models\Store;
use Modules\Stores\app\Models\StoreType;
use Modules\Transactions\app\Models\Transaction;
use Modules\Users\app\Models\User;
use Tests\TestCase;

class TransactionsWebTest extends TestCase
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

    private function createClient($storeId): Client
    {
        $client = Client::create([
            'client_name' => 'Alice',
            'client_last_name' => 'Smith',
            'client_document_type' => 'Cedula',
            'client_document_number' => '123456',
            'client_email' => 'alice@example.com',
            'client_phone_number' => '+1 987654321',
            'client_registration_date' => now(),
            'client_update_date' => now(),
            'client_active' => true,
            'client_type' => 'On-Term',
            'client_stores_ids' => json_encode([$storeId]),
        ]);

        ClientState::create([
            'client_state_client_id' => $client->id,
            'client_state_store_id' => $storeId,
            'client_state_amount' => 0,
            'client_state_state' => 'Settled',
            'client_state_last_transaction_date' => now(),
        ]);

        return $client;
    }

    private function createProduct($storeId, $name, $weight): Product
    {
        return Product::create([
            'product_store_id' => $storeId,
            'product_name' => $name,
            'product_type' => 0,
            'product_weight' => $weight,
            'product_value' => 500.00,
            'product_currency' => 'USD',
            'product_state' => $weight > 0 ? 'In Stock' : 'Out of Stock',
            'product_registration_date' => now(),
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('transactions.index'))->assertRedirect(route('login'));
        $this->get(route('transactions.quick'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_transactions_index(): void
    {
        $user = $this->createUser();
        $store = $this->createStore($user->id);
        $client = $this->createClient($store->id);

        Transaction::create([
            'transaction_client_id' => $client->id,
            'transaction_store_id' => $store->id,
            'transaction_transaction' => 'Buying',
            'transaction_amount' => 100.00,
            'transaction_state' => 'Debit',
            'transaction_registration_date' => now(),
            'transaction_description' => 'Test Transaction',
            'transaction_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('transactions.index'));

        $response->assertOk()
            ->assertViewIs('transactions.index')
            ->assertViewHas('transactions')
            ->assertSee('Test Transaction');
    }

    public function test_quick_transaction_view(): void
    {
        $user = $this->createUser();
        $response = $this->actingAs($user)->get(route('transactions.quick'));
        $response->assertOk()->assertViewIs('transactions.quick');
    }

    public function test_quick_store_buying(): void
    {
        $user = $this->createUser();
        $store = $this->createStore($user->id);
        $client = $this->createClient($store->id);
        $product = $this->createProduct($store->id, 'Gold 24K', 10.00);

        $response = $this->actingAs($user)->post(route('transactions.quick.store'), [
            'client_id' => $client->id,
            'direction' => 'buying',
            'total_value' => 1000.00,
            'amount_paid' => 400.00,
            'items' => [
                [
                    'product_id' => $product->id,
                    'weight' => 5.00,
                ]
            ]
        ]);

        $response->assertRedirect(route('customers.show', $client->id));

        // Business buys gold -> product weight increases to 15.00
        $product->refresh();
        $this->assertEquals(15.0000, (float)$product->product_weight);

        // Client gets money in favor. Total value=1000, paid=400, remaining=600 in favor of client.
        $state = ClientState::where('client_state_client_id', $client->id)->first();
        $this->assertEquals(600.0000, (float)$state->client_state_amount);
        $this->assertEquals('Favor', $state->client_state_state);

        $this->assertDatabaseHas('client_order', [
            'client_order_client_id' => $client->id,
            'client_order_value' => 1000.00,
            'client_order_state' => 'Favor',
        ]);

        $this->assertDatabaseHas('transaction', [
            'transaction_client_id' => $client->id,
            'transaction_amount' => 1000.00,
            'transaction_transaction' => 'Selling', // buying direction maps to "Selling" txType (business perspective on inventory/financials)
            'transaction_state' => 'Favor',
        ]);
    }

    public function test_quick_store_selling_insufficient_stock(): void
    {
        $user = $this->createUser();
        $store = $this->createStore($user->id);
        $client = $this->createClient($store->id);
        $product = $this->createProduct($store->id, 'Gold 24K', 3.00);

        $response = $this->actingAs($user)->from(route('transactions.quick'))->post(route('transactions.quick.store'), [
            'client_id' => $client->id,
            'direction' => 'selling',
            'total_value' => 1000.00,
            'amount_paid' => 400.00,
            'items' => [
                [
                    'product_id' => $product->id,
                    'weight' => 5.00, // exceeds 3.00
                ]
            ]
        ]);

        $response->assertRedirect(route('transactions.quick'))
            ->assertSessionHasErrors(['items.0.weight']);
    }

    public function test_quick_store_selling_sufficient_stock(): void
    {
        $user = $this->createUser();
        $store = $this->createStore($user->id);
        $client = $this->createClient($store->id);
        $product = $this->createProduct($store->id, 'Gold 24K', 10.00);

        $response = $this->actingAs($user)->post(route('transactions.quick.store'), [
            'client_id' => $client->id,
            'direction' => 'selling',
            'total_value' => 1000.00,
            'amount_paid' => 400.00,
            'items' => [
                [
                    'product_id' => $product->id,
                    'weight' => 5.00,
                ]
            ]
        ]);

        $response->assertRedirect(route('customers.show', $client->id));

        $product->refresh();
        $this->assertEquals(5.0000, (float)$product->product_weight);

        // Client owes business. Total value=1000, paid=400, remaining=600 debt (negative)
        $state = ClientState::where('client_state_client_id', $client->id)->first();
        $this->assertEquals(-600.0000, (float)$state->client_state_amount);
        $this->assertEquals('Debit', $state->client_state_state);
    }

    public function test_legacy_form_selling_creates_debt(): void
    {
        $user = $this->createUser();
        $store = $this->createStore($user->id);
        $client = $this->createClient($store->id);

        $response = $this->actingAs($user)->post(route('transactions.store', $client->id), [
            'type' => 'Selling',
            'amount' => 250.00,
            'description' => 'Gold jewelry credit',
        ]);

        $response->assertRedirect(route('customers.show', $client->id))
            ->assertSessionHas('status', 'Debt of $250.00 registered.');

        $state = ClientState::where('client_state_client_id', $client->id)->first();
        $this->assertEquals(-250.0000, (float)$state->client_state_amount);
        $this->assertEquals('Debit', $state->client_state_state);

        $this->assertDatabaseHas('transaction', [
            'transaction_client_id' => $client->id,
            'transaction_amount' => 250.00,
            'transaction_transaction' => 'Buying',
            'transaction_state' => 'Debit',
        ]);
    }

    public function test_legacy_form_paying_reduces_debt(): void
    {
        $user = $this->createUser();
        $store = $this->createStore($user->id);
        $client = $this->createClient($store->id);

        // Initialize state to Debit -300
        ClientState::where('client_state_client_id', $client->id)
            ->where('client_state_store_id', $store->id)
            ->update([
                'client_state_amount' => -300.00,
                'client_state_state' => 'Debit',
                'client_state_last_transaction_date' => now(),
            ]);

        // Add a mock ClientOrder for linking
        ClientOrder::create([
            'client_order_client_id' => $client->id,
            'client_order_store_id' => $store->id,
            'client_order_value' => 300.00,
            'client_order_state' => 'Debit',
            'client_order_registration_date' => now(),
            'client_order_date_due' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('transactions.store', $client->id), [
            'type' => 'Paying',
            'amount' => 200.00,
        ]);

        $response->assertRedirect(route('customers.show', $client->id))
            ->assertSessionHas('status', 'Payment of $200.00 registered.');

        $state = $client->states()->first();
        $this->assertEquals(-100.0000, (float)$state->client_state_amount);
        $this->assertEquals('Debit', $state->client_state_state);
    }

    public function test_legacy_form_buying_adds_favor(): void
    {
        $user = $this->createUser();
        $store = $this->createStore($user->id);
        $client = $this->createClient($store->id);

        $response = $this->actingAs($user)->post(route('transactions.store', $client->id), [
            'type' => 'Buying',
            'amount' => 400.00,
        ]);

        $response->assertRedirect(route('customers.show', $client->id));

        $state = $client->states()->first();
        $this->assertEquals(400.0000, (float)$state->client_state_amount);
        $this->assertEquals('Favor', $state->client_state_state);
    }

    public function test_legacy_form_retrieving_delivers_money(): void
    {
        $user = $this->createUser();
        $store = $this->createStore($user->id);
        $client = $this->createClient($store->id);

        // Initialize state to Favor 500
        ClientState::where('client_state_client_id', $client->id)
            ->where('client_state_store_id', $store->id)
            ->update([
                'client_state_amount' => 500.00,
                'client_state_state' => 'Favor',
                'client_state_last_transaction_date' => now(),
            ]);

        $response = $this->actingAs($user)->post(route('transactions.store', $client->id), [
            'type' => 'Retriving',
            'amount' => 300.00,
            'recipient_name' => 'Agent Smith',
        ]);

        $response->assertRedirect(route('customers.show', $client->id));

        $state = $client->states()->first();
        $this->assertEquals(200.0000, (float)$state->client_state_amount);
        $this->assertEquals('Favor', $state->client_state_state);
    }

    public function test_legacy_form_settle_closes_account(): void
    {
        $user = $this->createUser();
        $store = $this->createStore($user->id);
        $client = $this->createClient($store->id);

        ClientState::where('client_state_client_id', $client->id)
            ->where('client_state_store_id', $store->id)
            ->update([
                'client_state_amount' => -150.00,
                'client_state_state' => 'Debit',
                'client_state_last_transaction_date' => now(),
            ]);

        $response = $this->actingAs($user)->post(route('transactions.store', $client->id), [
            'type' => 'Settle',
        ]);

        $response->assertRedirect(route('customers.show', $client->id));

        $state = $client->states()->first();
        $this->assertEquals(0.0000, (float)$state->client_state_amount);
        $this->assertEquals('Settled', $state->client_state_state);
    }
}
