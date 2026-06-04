<?php

namespace Modules\Stores\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Stores\app\Models\StoreType;

class StoreTypeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => StoreType::all()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'store_type_description' => ['required', 'string', 'max:255'],
        ]);

        $storeType = StoreType::create($data);

        return response()->json(['data' => $storeType], 201);
    }

    public function show(StoreType $storeType): JsonResponse
    {
        return response()->json(['data' => $storeType]);
    }

    public function update(Request $request, StoreType $storeType): JsonResponse
    {
        $data = $request->validate([
            'store_type_description' => ['sometimes', 'string', 'max:255'],
        ]);

        $storeType->update($data);

        return response()->json(['data' => $storeType]);
    }

    public function destroy(StoreType $storeType): JsonResponse
    {
        $storeType->delete();

        return response()->json(['message' => 'Store type deleted successfully.']);
    }
}
