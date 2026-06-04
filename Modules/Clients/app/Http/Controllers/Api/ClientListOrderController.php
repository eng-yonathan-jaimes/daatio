<?php

namespace Modules\Clients\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Clients\app\Models\ClientListOrder;

class ClientListOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $items = ClientListOrder::with(['client', 'store', 'order', 'product'])->paginate($request->query('per_page', 15));

        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'client_list_order_client_id' => ['required', 'integer', 'min:1'],
            'client_list_order_store_id' => ['required', 'integer', 'min:1'],
            'client_list_order_id' => ['required', 'integer', 'min:1'],
            'client_list_order_product_id' => ['required', 'integer', 'min:1'],
            'client_list_order_weight' => ['required', 'numeric', 'min:0'],
            'client_list_order_value' => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ]);

        $data['client_list_order_registration_date'] = now();

        $item = ClientListOrder::create($data);

        return response()->json(['data' => $item->load(['client', 'store', 'order', 'product'])], 201);
    }

    public function show(ClientListOrder $clientListOrder): JsonResponse
    {
        return response()->json(['data' => $clientListOrder->load(['client', 'store', 'order', 'product'])]);
    }

    public function update(Request $request, ClientListOrder $clientListOrder): JsonResponse
    {
        $data = $request->validate([
            'client_list_order_client_id' => ['sometimes', 'integer', 'min:1'],
            'client_list_order_store_id' => ['sometimes', 'integer', 'min:1'],
            'client_list_order_id' => ['sometimes', 'integer', 'min:1'],
            'client_list_order_product_id' => ['sometimes', 'integer', 'min:1'],
            'client_list_order_weight' => ['sometimes', 'numeric', 'min:0'],
            'client_list_order_value' => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ]);

        $clientListOrder->update($data);

        return response()->json(['data' => $clientListOrder->load(['client', 'store', 'order', 'product'])]);
    }

    public function destroy(ClientListOrder $clientListOrder): JsonResponse
    {
        $clientListOrder->delete();

        return response()->json(['message' => 'Client list order item deleted successfully.']);
    }
}
