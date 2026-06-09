<?php

namespace Tests\Feature\Clients;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Clients\app\Models\Client;
use Modules\Clients\app\Models\ClientState;
use Modules\Stores\app\Models\Store;
use Modules\Stores\app\Models\StoreType;
use Modules\Users\app\Models\User;
use Tests\TestCase;

class CustomerWebTest extends TestCase
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
        $this->get(route('customers.index'))->assertRedirect(route('login'));
        $this->get(route('customers.create'))->assertRedirect(route('login'));
        $this->post(route('customers.store'), [])->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_customers_index(): void
    {
        $user = $this->createUser();
        $store = $this->createStore($user->id);

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
            'client_stores_ids' => json_encode([$store->id]),
        ]);

        ClientState::create([
            'client_state_client_id' => $client->id,
            'client_state_store_id' => $store->id,
            'client_state_amount' => 0,
            'client_state_state' => 'Settled',
            'client_state_last_transaction_date' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('customers.index'));

        $response->assertOk()
            ->assertViewIs('customers.index')
            ->assertViewHas('customers')
            ->assertSee('Alice')
            ->assertSee('Smith');
    }

    public function test_can_search_customers_by_name_or_phone(): void
    {
        $user = $this->createUser();
        $store = $this->createStore($user->id);

        $client1 = Client::create([
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
            'client_stores_ids' => json_encode([$store->id]),
        ]);

        ClientState::create([
            'client_state_client_id' => $client1->id,
            'client_state_store_id' => $store->id,
            'client_state_amount' => 0,
            'client_state_state' => 'Settled',
            'client_state_last_transaction_date' => now(),
        ]);

        $client2 = Client::create([
            'client_name' => 'Bob',
            'client_last_name' => 'Jones',
            'client_document_type' => 'Cedula',
            'client_document_number' => '789101',
            'client_email' => 'bob@example.com',
            'client_phone_number' => '+1 555555555',
            'client_registration_date' => now(),
            'client_update_date' => now(),
            'client_active' => true,
            'client_type' => 'On-Term',
            'client_stores_ids' => json_encode([$store->id]),
        ]);

        ClientState::create([
            'client_state_client_id' => $client2->id,
            'client_state_store_id' => $store->id,
            'client_state_amount' => 0,
            'client_state_state' => 'Settled',
            'client_state_last_transaction_date' => now(),
        ]);

        // Search for 'Alice'
        $response = $this->actingAs($user)->get(route('customers.index', ['search' => 'Alice']));
        $response->assertSee('Alice');
        $response->assertDontSee('Bob');

        // Search by phone
        $response = $this->actingAs($user)->get(route('customers.index', ['search' => '555']));
        $response->assertSee('Bob');
        $response->assertDontSee('Alice');
    }

    public function test_authenticated_user_can_create_customer(): void
    {
        $user = $this->createUser();
        $store = $this->createStore($user->id);

        $response = $this->actingAs($user)->post(route('customers.store'), [
            'first_name' => 'Charlie',
            'last_name' => 'Brown',
            'country_code' => '+1',
            'phone' => '111222333',
            'email' => 'charlie@example.com',
            'document_type' => 'Passport',
            'document_number' => 'P12345',
        ]);

        $this->assertDatabaseHas('client', [
            'client_name' => 'Charlie',
            'client_last_name' => 'Brown',
            'client_phone_number' => '+1 111222333',
            'client_email' => 'charlie@example.com',
            'client_document_type' => 'Passport',
            'client_document_number' => 'P12345',
        ]);

        $client = Client::where('client_email', 'charlie@example.com')->first();

        $response->assertRedirect(route('customers.show', $client->id));

        $this->assertDatabaseHas('client_state', [
            'client_state_client_id' => $client->id,
            'client_state_store_id' => $store->id,
            'client_state_amount' => 0.0000,
            'client_state_state' => 'Settled',
        ]);
    }

    public function test_create_customer_validation(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post(route('customers.store'), []);

        $response->assertSessionHasErrors(['first_name', 'last_name', 'country_code', 'phone']);
    }

    public function test_authenticated_user_can_view_customer_detail(): void
    {
        $user = $this->createUser();
        $store = $this->createStore($user->id);

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
            'client_stores_ids' => json_encode([$store->id]),
        ]);

        ClientState::create([
            'client_state_client_id' => $client->id,
            'client_state_store_id' => $store->id,
            'client_state_amount' => 100.00,
            'client_state_state' => 'Favor',
            'client_state_last_transaction_date' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('customers.show', $client->id));

        $response->assertOk()
            ->assertViewIs('customers.show')
            ->assertViewHas('client')
            ->assertViewHas('balance')
            ->assertViewHas('transactions');
    }
}
