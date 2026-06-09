<?php

namespace Modules\Clients\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Clients\app\Models\ClientState;

class ClientStateController extends Controller
{
    public function index(Request $request)
    {
        return ClientState::paginate($request->query('per_page', 50));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_state_client_id' => ['required', 'exists:client,id'],
            'client_state_store_id' => ['required', 'exists:store,id'],
            'client_state_amount' => ['required', 'numeric'],
            'client_state_state' => ['required', 'string', 'in:Favor,Debit,Settled'],
        ]);
        $validated['client_state_last_transaction_date'] = now();
        return ClientState::create($validated);
    }

    public function show(ClientState $state)
    {
        return $state;
    }

    public function update(Request $request, ClientState $state)
    {
        $state->update($request->all());
        return $state;
    }

    public function destroy(ClientState $state)
    {
        $state->delete();
        return response()->noContent();
    }
}
