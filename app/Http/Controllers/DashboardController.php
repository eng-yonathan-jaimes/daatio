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

        $totalCustomers = Client::where('client_active', true)
            ->whereHas('states', function ($q) use ($storeIds) {
                $q->whereIn('client_state_store_id', $storeIds);
            })->count();

        // Single grouped query instead of two separate queries + PHP sum
        $stateSums = ClientState::whereIn('client_state_store_id', $storeIds)
            ->whereIn('client_state_state', ['Debit', 'Favor'])
            ->selectRaw("
                client_state_state,
                SUM(client_state_amount) as total_amount
            ")
            ->groupBy('client_state_state')
            ->pluck('total_amount', 'client_state_state');

        // Debit amounts are negative; abs to get positive debt total
        $totalDebt = abs($stateSums['Debit'] ?? 0);
        $totalFavor = (float)($stateSums['Favor'] ?? 0);

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
