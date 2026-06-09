<?php

namespace Modules\Clients\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Clients\app\Models\Client;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        return Client::orderBy('client_name')
            ->paginate($request->query('per_page', 25));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'client_last_name' => ['required', 'string', 'max:255'],
            'client_document_type' => ['required', 'string', 'in:Cedula,Passport,PPT'],
            'client_document_number' => ['required', 'string', 'max:255'],
            'client_email' => ['nullable', 'email', 'max:255'],
            'client_phone_number' => ['nullable', 'string', 'max:255'],
            'client_active' => ['required', 'boolean'],
            'client_type' => ['required', 'string', 'in:Prompt,On-Term,Late,Partial,Deadbeat'],
            'client_stores_ids' => ['sometimes', 'array'],
        ]);

        $validated['client_registration_date'] = now();
        $validated['client_update_date'] = now();

        return Client::create($validated);
    }

    public function show(Client $client)
    {
        return $client;
    }

    public function update(Request $request, Client $client)
    {
        $client->update($request->all());
        return $client;
    }

    public function destroy(Client $client)
    {
        $client->update(['client_active' => false]);
        return response()->noContent();
    }
}
