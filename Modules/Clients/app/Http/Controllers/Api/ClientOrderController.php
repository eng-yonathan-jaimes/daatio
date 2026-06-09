<?php

namespace Modules\Clients\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Clients\app\Models\ClientOrder;

class ClientOrderController extends Controller
{
    public function index(Request $request)
    {
        return ClientOrder::with('items.product')
            ->orderBy('client_order_registration_date', 'desc')
            ->paginate($request->query('per_page', 25));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_order_client_id' => ['required', 'exists:client,id'],
            'client_order_store_id' => ['required', 'exists:store,id'],
            'client_order_value' => ['required', 'numeric', 'min:0'],
            'client_order_state' => ['required', 'string'],
        ]);
        $validated['client_order_registration_date'] = now();
        $validated['client_order_date_due'] = now();
        return ClientOrder::create($validated);
    }

    public function show(ClientOrder $order)
    {
        return $order->load('items.product');
    }

    public function update(Request $request, ClientOrder $order)
    {
        $order->update($request->all());
        return $order;
    }

    public function destroy(ClientOrder $order)
    {
        $order->delete();
        return response()->noContent();
    }
}
