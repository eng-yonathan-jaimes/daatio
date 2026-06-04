<?php

namespace Modules\Clients\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Clients\app\Models\Client;

class ClientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $clients = Client::paginate($request->query('per_page', 15));

        return response()->json($clients);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'client_last_name' => ['required', 'string', 'max:255'],
            'client_document_type' => ['required', 'string', 'in:Cedula,Passport,PPT'],
            'client_document_number' => ['required', 'string', 'max:255'],
            'client_email' => ['sometimes', 'email', 'max:255'],
            'client_phone_number' => ['sometimes', 'string', 'max:255'],
            'client_active' => ['required', 'boolean'],
            'client_type' => ['required', 'string', 'in:Prompt,On-Term,Late,Partial,Deadbeat'],
            'client_stores_ids' => ['sometimes', 'array'],
        ]);

        $data['client_registration_date'] = now();
        $data['client_update_date'] = now();

        $client = Client::create($data);

        return response()->json(['data' => $client], 201);
    }

    public function show(Client $client): JsonResponse
    {
        return response()->json(['data' => $client]);
    }

    public function update(Request $request, Client $client): JsonResponse
    {
        $data = $request->validate([
            'client_name' => ['sometimes', 'string', 'max:255'],
            'client_last_name' => ['sometimes', 'string', 'max:255'],
            'client_document_type' => ['sometimes', 'string', 'in:Cedula,Passport,PPT'],
            'client_document_number' => ['sometimes', 'string', 'max:255'],
            'client_email' => ['sometimes', 'email', 'max:255'],
            'client_phone_number' => ['sometimes', 'string', 'max:255'],
            'client_active' => ['sometimes', 'boolean'],
            'client_type' => ['sometimes', 'string', 'in:Prompt,On-Term,Late,Partial,Deadbeat'],
            'client_stores_ids' => ['sometimes', 'array'],
        ]);

        $data['client_update_date'] = now();

        $client->update($data);

        return response()->json(['data' => $client]);
    }

    public function destroy(Client $client): JsonResponse
    {
        $client->delete();

        return response()->json(['message' => 'Client deleted successfully.']);
    }
}
