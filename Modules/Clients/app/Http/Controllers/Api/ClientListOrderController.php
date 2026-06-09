<?php

namespace Modules\Clients\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Clients\app\Models\ClientListOrder;

class ClientListOrderController extends Controller
{
    public function index(Request $request)
    {
        return ClientListOrder::paginate($request->query('per_page', 50));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_list_order_client_id' => ['required', 'exists:client,id'],
            'client_list_order_store_id' => ['required', 'exists:store,id'],
            'client_list_order_id' => ['required', 'exists:client_order,id'],
            'client_list_order_product_id' => ['required', 'exists:product,id'],
            'client_list_order_weight' => ['required', 'numeric', 'min:0'],
            'client_list_order_value' => ['nullable', 'numeric', 'min:0'],
        ]);
        $validated['client_list_order_registration_date'] = now();
        return ClientListOrder::create($validated);
    }

    public function show(ClientListOrder $item)
    {
        return $item;
    }

    public function update(Request $request, ClientListOrder $item)
    {
        $item->update($request->all());
        return $item;
    }

    public function destroy(ClientListOrder $item)
    {
        $item->delete();
        return response()->noContent();
    }
}
