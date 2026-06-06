<?php

namespace Modules\Stores\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Stores\app\Models\Store;
use Modules\Stores\app\Models\StoreType;

class StoresController extends Controller
{
    public function index()
    {
        $stores = Store::where('store_user_id', Auth::id())
            ->with('type')
            ->orderBy('store_name')
            ->get();

        $types = StoreType::all();

        return view('stores.index', compact('stores', 'types'));
    }

    public function create()
    {
        $types = StoreType::all();
        return view('stores.create', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'store_address' => ['nullable', 'string', 'max:255'],
            'store_type_id' => ['required', 'exists:store_type,id'],
            'store_location' => ['required', 'in:Physical,Online,Both'],
        ]);

        Store::create([
            'store_user_id' => Auth::id(),
            'store_name' => $validated['store_name'],
            'store_address' => $validated['store_address'] ?? '',
            'store_type_id' => $validated['store_type_id'],
            'store_location' => $validated['store_location'],
            'store_active' => true,
            'store_registration_date' => now(),
            'store_update_date' => now(),
        ]);

        return redirect()->route('stores.index')
            ->with('status', 'Store created successfully.');
    }

    public function edit($id)
    {
        $store = Store::where('store_user_id', Auth::id())->findOrFail($id);
        $types = StoreType::all();
        return view('stores.edit', compact('store', 'types'));
    }

    public function update(Request $request, $id)
    {
        $store = Store::where('store_user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'store_address' => ['nullable', 'string', 'max:255'],
            'store_type_id' => ['required', 'exists:store_type,id'],
            'store_location' => ['required', 'in:Physical,Online,Both'],
        ]);

        $store->update([
            'store_name' => $validated['store_name'],
            'store_address' => $validated['store_address'] ?? '',
            'store_type_id' => $validated['store_type_id'],
            'store_location' => $validated['store_location'],
            'store_update_date' => now(),
        ]);

        return redirect()->route('stores.index')
            ->with('status', 'Store updated successfully.');
    }

    public function toggle($id)
    {
        $store = Store::where('store_user_id', Auth::id())->findOrFail($id);
        $store->update([
            'store_active' => !$store->store_active,
            'store_update_date' => now(),
        ]);

        return redirect()->route('stores.index')
            ->with('status', 'Store ' . ($store->store_active ? 'activated' : 'deactivated') . '.');
    }

    public function storeType(Request $request)
    {
        $validated = $request->validate([
            'store_type_description' => ['required', 'string', 'max:255'],
        ]);

        StoreType::create(['store_type_description' => $validated['store_type_description']]);

        return redirect()->route('stores.index')
            ->with('status', 'Store type added.');
    }
}
