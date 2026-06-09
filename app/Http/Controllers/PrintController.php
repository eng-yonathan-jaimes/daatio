<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Clients\app\Models\Client;
use Modules\Stores\app\Models\Store;
use Modules\Transactions\app\Models\Transaction;

class PrintController extends Controller
{
    public function customerHistory($id)
    {
        $user = Auth::user();
        $storeIds = Store::where('store_user_id', $user->id)->pluck('id');

        $client = Client::whereHas('states', function ($q) use ($storeIds) {
            $q->whereIn('client_state_store_id', $storeIds);
        })->with(['transactions' => function ($q) use ($storeIds) {
            $q->whereIn('transaction_store_id', $storeIds)
              ->with('user')
              ->orderBy('transaction_registration_date', 'desc');
        }, 'states' => function ($q) use ($storeIds) {
            $q->whereIn('client_state_store_id', $storeIds);
        }])->findOrFail($id);

        $balance = $client->states->first();

        return view('print.customer-history', compact('client', 'balance'));
    }

    public function transactions(Request $request)
    {
        $user = Auth::user();
        $storeIds = Store::where('store_user_id', $user->id)->pluck('id');

        $type = $request->input('type');

        $transactions = Transaction::whereIn('transaction_store_id', $storeIds)
            ->with('client', 'user')
            ->when($type, fn($q) => $q->where('transaction_transaction', $type))
            ->orderBy('transaction_registration_date', 'desc')
            ->get();

        return view('print.transactions', compact('transactions', 'type'));
    }

    public function customerList(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();
        $storeIds = Store::where('store_user_id', $user->id)->pluck('id');

        $customers = Client::query()
            ->where('client_active', true)
            ->whereHas('states', function ($q) use ($storeIds) {
                $q->whereIn('client_state_store_id', $storeIds);
            })
            ->with(['states' => function ($q) use ($storeIds) {
                $q->whereIn('client_state_store_id', $storeIds);
            }]);

        if ($search) {
            $customers->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                    ->orWhere('client_last_name', 'like', "%{$search}%")
                    ->orWhere('client_phone_number', 'like', "%{$search}%");
            });
        }

        $customers = $customers->orderBy('client_name')->get();

        return view('print.customer-list', compact('customers'));
    }
}
