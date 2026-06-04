<?php

namespace Modules\Clients\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Clients\app\Models\ClientState;

class ClientStateController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $states = ClientState::with(['client', 'store'])->paginate($request->query('per_page', 15));

        return response()->json($states);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'client_state_client_id' => ['required', 'integer', 'min:1'],
            'client_state_store_id' => ['required', 'integer', 'min:1'],
            'client_state_amount' => ['required', 'numeric', 'min:0'],
            'client_state_state' => ['required', 'string', 'in:Favor,Debit,Settled'],
            'client_state_last_transaction_date' => ['sometimes', 'date'],
        ]);

        $state = ClientState::create($data);

        return response()->json(['data' => $state->load(['client', 'store'])], 201);
    }

    public function show(ClientState $clientState): JsonResponse
    {
        return response()->json(['data' => $clientState->load(['client', 'store'])]);
    }

public function update(Request $request, ClientState $clientState): JsonResponse
    {
        $data = $request->validate([
            'client_state_client_id' => ['sometimes', 'integer', 'min:1'],
            'client_state_store_id' => ['sometimes', 'integer', 'min:1'],
            'client_state_amount' => ['sometimes', 'numeric', 'min:0'],
            'client_state_state' => ['sometimes', 'string', 'in:Favor,Debit,Settled'],
            'client_state_last_transaction_date' => ['sometimes', 'date'],
        ]);

        $clientState->update($data);

        return response()->json(['data' => $clientState->load(['client', 'store'])]);
    }

    public function destroy(ClientState $clientState): JsonResponse
    {
        $clientState->delete();

        return response()->json(['message' => 'Client state deleted successfully.']);
    }
}
