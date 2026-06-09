<?php

namespace Tests\Feature\Stores;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Stores\app\Models\Store;
use Modules\Stores\app\Models\StoreType;
use Modules\Users\app\Models\User;
use Tests\TestCase;

class StoreWebTest extends TestCase
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

    private function createStoreType(): StoreType
    {
        return StoreType::create([
            'store_type_description' => 'Gold Shop',
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('stores.index'))->assertRedirect(route('login'));
        $this->get(route('stores.create'))->assertRedirect(route('login'));
        $this->post(route('stores.store'), [])->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_stores_index(): void
    {
        $user = $this->createUser();
        $type = $this->createStoreType();

        Store::create([
            'store_user_id' => $user->id,
            'store_name' => 'Gold Store',
            'store_address' => '123 Street',
            'store_type_id' => $type->id,
            'store_location' => 'Physical',
            'store_active' => true,
            'store_registration_date' => now(),
            'store_update_date' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('stores.index'));

        $response->assertOk()
            ->assertViewIs('stores.index')
            ->assertViewHas('stores')
            ->assertViewHas('types');
    }

    public function test_authenticated_user_can_create_store(): void
    {
        $user = $this->createUser();
        $type = $this->createStoreType();

        $response = $this->actingAs($user)->post(route('stores.store'), [
            'store_name' => 'New Store',
            'store_address' => '456 Avenue',
            'store_type_id' => $type->id,
            'store_location' => 'Both',
        ]);

        $response->assertRedirect(route('stores.index'))
            ->assertSessionHas('status', 'Store created successfully.');

        $this->assertDatabaseHas('store', [
            'store_user_id' => $user->id,
            'store_name' => 'New Store',
            'store_type_id' => $type->id,
            'store_location' => 'Both',
            'store_active' => 1,
        ]);
    }

    public function test_create_store_validation(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post(route('stores.store'), []);

        $response->assertSessionHasErrors(['store_name', 'store_type_id', 'store_location']);
    }

    public function test_authenticated_user_can_edit_and_update_store(): void
    {
        $user = $this->createUser();
        $type = $this->createStoreType();

        $store = Store::create([
            'store_user_id' => $user->id,
            'store_name' => 'Gold Store',
            'store_address' => '123 Street',
            'store_type_id' => $type->id,
            'store_location' => 'Physical',
            'store_active' => true,
            'store_registration_date' => now(),
            'store_update_date' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('stores.edit', $store->id));
        $response->assertOk()->assertViewIs('stores.edit');

        $response = $this->actingAs($user)->post(route('stores.update', $store->id), [
            'store_name' => 'Updated Store Name',
            'store_address' => '789 Road',
            'store_type_id' => $type->id,
            'store_location' => 'Online',
        ]);

        $response->assertRedirect(route('stores.index'))
            ->assertSessionHas('status', 'Store updated successfully.');

        $this->assertDatabaseHas('store', [
            'id' => $store->id,
            'store_name' => 'Updated Store Name',
            'store_location' => 'Online',
        ]);
    }

    public function test_authenticated_user_can_toggle_store_active_status(): void
    {
        $user = $this->createUser();
        $type = $this->createStoreType();

        $store = Store::create([
            'store_user_id' => $user->id,
            'store_name' => 'Gold Store',
            'store_address' => '123 Street',
            'store_type_id' => $type->id,
            'store_location' => 'Physical',
            'store_active' => true,
            'store_registration_date' => now(),
            'store_update_date' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('stores.toggle', $store->id));

        $response->assertRedirect(route('stores.index'))
            ->assertSessionHas('status', 'Store deactivated.');

        $this->assertDatabaseHas('store', [
            'id' => $store->id,
            'store_active' => 0,
        ]);
    }

    public function test_authenticated_user_can_create_store_type(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post(route('stores.types.store'), [
            'store_type_description' => 'Platinum Shop',
        ]);

        $response->assertRedirect(route('stores.index'))
            ->assertSessionHas('status', 'Store type added.');

        $this->assertDatabaseHas('store_type', [
            'store_type_description' => 'Platinum Shop',
        ]);
    }
}
