<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Clients\app\Models\Client;
use Modules\Clients\app\Models\ClientState;
use Modules\Stores\app\Models\Store;
use Modules\Transactions\app\Models\Transaction;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();
        $storeIds = Store::where('store_user_id', $user->id)->pluck('id');

        $customers = Client::query()
            ->where('client_active', true)
            ->whereHas('states', function ($q) use ($storeIds) {
                $q->whereIn('client_state_store_id', $storeIds);
            });

        if ($search) {
            $customers->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                    ->orWhere('client_last_name', 'like', "%{$search}%")
                    ->orWhere('client_phone_number', 'like', "%{$search}%");
            });
        }

        $customers = $customers->orderBy('client_name')->get();

        return view('customers.index', compact('customers', 'search'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'country_code' => ['required', 'string', 'max:5'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'document_type' => ['nullable', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:255'],
        ]);

        $fullPhone = $validated['country_code'] . ' ' . $validated['phone'];

        $user = Auth::user();
        $store = Store::where('store_user_id', $user->id)
            ->where('store_active', true)
            ->first();

        $client = Client::create([
            'client_name' => $validated['first_name'],
            'client_last_name' => $validated['last_name'],
            'client_phone_number' => $fullPhone,
            'client_email' => $validated['email'] ?? '',
            'client_document_type' => $validated['document_type'] ?? 'Cedula',
            'client_document_number' => $validated['document_number'] ?? '',
            'client_active' => true,
            'client_type' => 'On-Term',
            'client_stores_ids' => $store ? json_encode([$store->id]) : json_encode([]),
            'client_registration_date' => now(),
            'client_update_date' => now(),
        ]);

        if ($store) {
            ClientState::create([
                'client_state_client_id' => $client->id,
                'client_state_store_id' => $store->id,
                'client_state_amount' => 0,
                'client_state_state' => 'Settled',
                'client_state_last_transaction_date' => now(),
            ]);
        }

        return redirect()->route('customers.show', $client->id)
            ->with('status', 'Customer created successfully.');
    }

    public function show($id)
    {
        $user = Auth::user();
        $storeIds = Store::where('store_user_id', $user->id)->pluck('id');

        $client = Client::whereHas('states', function ($q) use ($storeIds) {
            $q->whereIn('client_state_store_id', $storeIds);
        })->with(['states' => function ($q) use ($storeIds) {
            $q->whereIn('client_state_store_id', $storeIds);
        }, 'transactions' => function ($q) use ($storeIds) {
            $q->whereIn('transaction_store_id', $storeIds)
                ->with('user')
                ->orderBy('transaction_registration_date', 'desc');
        }, 'orders' => function ($q) use ($storeIds) {
            $q->whereIn('client_order_store_id', $storeIds)
                ->with('items.product')
                ->orderBy('client_order_registration_date', 'desc');
        }])->findOrFail($id);

        $balance = $client->states->first();

        $transactions = $client->transactions;

        return view('customers.show', compact('client', 'balance', 'transactions'));
    }
}
