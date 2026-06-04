<?php

namespace Modules\Clients\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Clients\app\Models\ClientOrder;

class ClientOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = ClientOrder::with(['client', 'store'])->paginate($request->query('per_page', 15));

        return response()->json($orders);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'client_order_client_id' => ['required', 'integer', 'min:1'],
            'client_order_store_id' => ['required', 'integer', 'min:1'],
            'client_order_value' => ['required', 'numeric', 'min:0'],
            'client_order_state' => ['required', 'string', 'in:Favor,Debit,Settled'],
            'client_order_date_due' => ['sometimes', 'date'],
        ]);

        $data['client_order_registration_date'] = now();

        $order = ClientOrder::create($data);

        return response()->json(['data' => $order->load(['client', 'store'])], 201);
    }

    public function show(ClientOrder $clientOrder): JsonResponse
    {
        return response()->json(['data' => $clientOrder->load(['client', 'store', 'items', 'transactions'])]);
    }

    public function update(Request $request, ClientOrder $clientOrder): JsonResponse
    {
        $data = $request->validate([
            'client_order_client_id' => ['sometimes', 'integer', 'min:1'],
            'client_order_store_id' => ['sometimes', 'integer', 'min:1'],
            'client_order_value' => ['sometimes', 'numeric', 'min:0'],
            'client_order_state' => ['sometimes', 'string', 'in:Favor,Debit,Settled'],
            'client_order_date_due' => ['sometimes', 'date'],
        ]);

        $clientOrder->update($data);

        return response()->json(['data' => $clientOrder->load(['client', 'store'])]);
    }

    public function destroy(ClientOrder $clientOrder): JsonResponse
    {
        $clientOrder->delete();

        return response()->json(['message' => 'Client order deleted successfully.']);
    }
}
