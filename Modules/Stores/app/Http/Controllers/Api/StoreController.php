<?php

namespace Modules\Stores\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Stores\app\Models\Store;

class StoreController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $stores = Store::with(['user', 'type'])->paginate($request->query('per_page', 15));

        return response()->json($stores);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'store_user_id' => ['required', 'integer', 'min:1'],
            'store_name' => ['required', 'string', 'max:255'],
            'store_active' => ['required', 'boolean'],
            'store_address' => ['sometimes', 'string', 'max:200'],
            'store_type_id' => ['required', 'integer', 'min:1'],
            'store_location' => ['required', 'string', 'in:Physical,Online,Both'],
        ]);

        $data['store_update_date'] = now();

        $store = Store::create($data);

        return response()->json(['data' => $store->load(['user', 'type'])], 201);
    }

    public function show(Store $store): JsonResponse
    {
        return response()->json(['data' => $store->load(['user', 'type'])]);
    }

    public function update(Request $request, Store $store): JsonResponse
    {
        $data = $request->validate([
            'store_user_id' => ['sometimes', 'integer', 'min:1'],
            'store_name' => ['sometimes', 'string', 'max:255'],
            'store_active' => ['sometimes', 'boolean'],
            'store_address' => ['sometimes', 'string', 'max:200'],
            'store_type_id' => ['sometimes', 'integer', 'min:1'],
            'store_location' => ['sometimes', 'string', 'in:Physical,Online,Both'],
        ]);

        $data['store_update_date'] = now();

        $store->update($data);

        return response()->json(['data' => $store->load(['user', 'type'])]);
    }

    public function destroy(Store $store): JsonResponse
    {
        $store->delete();

        return response()->json(['message' => 'Store deleted successfully.']);
    }
}
