<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Clients\app\Models\Client;
use Modules\Clients\app\Models\ClientState;
use Modules\Stores\app\Models\Store;
use Modules\Subscriptions\app\Models\UserSubscription;
use Modules\Transactions\app\Models\Transaction;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $storeIds = Store::where('store_user_id', $user->id)
            ->where('store_active', true)
            ->pluck('id');

        $totalCustomers = Client::where('client_active', true)->count();

        $debtState = ClientState::whereIn('client_state_store_id', $storeIds)
            ->where('client_state_state', 'Debit')
            ->get();

        $totalDebt = $debtState->sum(function ($s) {
            return abs($s->client_state_amount);
        });

        $totalFavor = ClientState::whereIn('client_state_store_id', $storeIds)
            ->where('client_state_state', 'Favor')
            ->sum('client_state_amount');

        $totalPaid = Transaction::whereIn('transaction_store_id', $storeIds)
            ->where('transaction_transaction', 'Paying')
            ->sum('transaction_amount');

        $outstandingBalance = $totalDebt;

        $search = $request->input('search');

        $debtorsQuery = Client::query()
            ->where('client_active', true)
            ->whereHas('states', function ($q) use ($storeIds) {
                $q->whereIn('client_state_store_id', $storeIds)
                    ->where('client_state_state', 'Debit');
            })
            ->with(['states' => function ($q) use ($storeIds) {
                $q->whereIn('client_state_store_id', $storeIds)
                    ->where('client_state_state', 'Debit');
            }]);

        if ($search) {
            $debtorsQuery->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                    ->orWhere('client_last_name', 'like', "%{$search}%")
                    ->orWhere('client_phone_number', 'like', "%{$search}%");
            });
        }

        $debtors = $debtorsQuery->orderBy('client_name')->get();

        $subscription = UserSubscription::where('user_subscription_user_id', $user->id)
            ->with('subscription')
            ->latest('user_subscription_start_date')
            ->first();

        return view('dashboard.index', compact(
            'totalCustomers',
            'totalDebt',
            'totalPaid',
            'outstandingBalance',
            'debtors',
            'search',
            'subscription'
        ));
    }
}
