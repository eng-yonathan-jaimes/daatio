<?php

namespace Modules\Transactions\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Clients\app\Models\Client;
use Modules\Clients\app\Models\ClientListOrder;
use Modules\Clients\app\Models\ClientOrder;
use Modules\Clients\app\Models\ClientState;
use Modules\Products\app\Models\Product;
use Modules\Stores\app\Models\Store;
use Modules\Transactions\app\Models\Transaction;

class TransactionsController extends Controller
{
    private function createTransaction(array $data): void
    {
        Transaction::create(array_merge($data, [
            'transaction_user_id' => Auth::id(),
        ]));
    }

    private function computeState(string $amount): string
    {
        if (bccomp($amount, '0', 4) > 0) {
            return 'Favor';
        }
        if (bccomp($amount, '0', 4) < 0) {
            return 'Debit';
        }
        return 'Settled';
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $storeIds = Store::where('store_user_id', $user->id)->pluck('id');

        $type = $request->input('type');

        $transactions = Transaction::whereIn('transaction_store_id', $storeIds)
            ->with('client', 'user')
            ->when($type, fn($q) => $q->where('transaction_transaction', $type))
            ->orderBy('transaction_registration_date', 'desc')
            ->get();

        return view('transactions.index', compact('transactions', 'type'));
    }

    public function create(Request $request, $customerId)
    {
        $user = Auth::user();
        $storeIds = Store::where('store_user_id', $user->id)->pluck('id');

        $client = Client::whereHas('states', function ($q) use ($storeIds) {
            $q->whereIn('client_state_store_id', $storeIds);
        })->findOrFail($customerId);

        $type = $request->input('type', 'Selling');

        $store = Store::where('store_user_id', $user->id)
            ->where('store_active', true)
            ->first();

        $state = ClientState::where('client_state_client_id', $client->id)
            ->where('client_state_store_id', $store?->id)
            ->first();

        return view('transactions.create', compact('client', 'type', 'state'));
    }

    public function quick()
    {
        return view('transactions.quick');
    }

    public function quickStore(Request $request)
    {
        $user = Auth::user();
        $store = Store::where('store_user_id', $user->id)
            ->where('store_active', true)
            ->first();

        if (!$store) {
            return back()->withErrors(['error' => 'No active store found.']);
        }

        $validated = $request->validate([
            'client_id' => ['required', 'exists:client,id'],
            'direction' => ['required', 'in:buying,selling'],
            'total_value' => ['required', 'numeric', 'min:0.01'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'items' => ['nullable', 'array'],
        ]);

        $storeIds = Store::where('store_user_id', $user->id)->pluck('id');

        $client = Client::whereHas('states', function ($q) use ($storeIds) {
            $q->whereIn('client_state_store_id', $storeIds);
        })->findOrFail($validated['client_id']);

        $totalValue = $validated['total_value'];
        $amountPaid = $validated['amount_paid'] ?? 0;

        if ($amountPaid > $totalValue) {
            return back()->withErrors(['amount_paid' => 'Amount paid cannot exceed the total value.'])->withInput();
        }

        $remaining = bcsub((string)$totalValue, (string)$amountPaid, 4);

        $state = ClientState::firstOrNew(
            [
                'client_state_client_id' => $client->id,
                'client_state_store_id' => $store->id,
            ],
            [
                'client_state_amount' => 0,
                'client_state_state' => 'Settled',
                'client_state_last_transaction_date' => now(),
            ]
        );

        // Direction = buying: business buys from client → type=Selling, creates Favor (positive)
        // Direction = selling: business sells to client → type=Buying, creates Debit (negative)
        if ($validated['direction'] === 'buying') {
            $txType = 'Selling';
        } else {
            $txType = 'Buying';
        }

        // Single batch fetch of ALL products referenced by this order
        $productIds = collect($validated['items'] ?? [])
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $products = [];
        if ($productIds) {
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        }

        // Validate stock for selling direction using cached products (0 queries)
        if ($validated['direction'] === 'selling' && !empty($validated['items'])) {
            $weightByProduct = [];
            foreach ($validated['items'] as $item) {
                if (empty($item['product_id']) || empty($item['weight'])) continue;
                if ($item['weight'] <= 0) continue;
                $pid = $item['product_id'];
                $weightByProduct[$pid] = ($weightByProduct[$pid] ?? 0) + (float)$item['weight'];
            }

            foreach ($weightByProduct as $pid => $totalWeight) {
                $product = $products[$pid] ?? null;
                if (!$product || $totalWeight > $product->product_weight) {
                    $name = $product ? $product->product_name : 'Unknown';
                    $avail = $product ? rtrim(rtrim(number_format($product->product_weight, 4), '0'), '.') : 0;
                    $idx = collect($validated['items'])->search(fn($it) => ($it['product_id'] ?? null) == $pid);
                    return back()->withErrors([
                        "items.{$idx}.weight" => "Not enough stock for {$name}. Available: {$avail}g."
                    ])->withInput();
                }
            }
        }

        // Determine order state based on remaining balance
        if (bccomp($remaining, '0', 4) > 0) {
            $orderState = $validated['direction'] === 'buying' ? 'Favor' : 'Debit';
            $txState = $orderState;
        } else {
            $orderState = 'Settled';
            $txState = 'Settled';
        }

        $order = ClientOrder::create([
            'client_order_client_id' => $client->id,
            'client_order_store_id' => $store->id,
            'client_order_value' => $totalValue,
            'client_order_state' => $orderState,
            'client_order_registration_date' => now(),
            'client_order_date_due' => now(),
        ]);

        // Create line items, update stock, and collect product names
        $productNames = [];
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                if (empty($item['product_id']) || empty($item['weight'])) continue;
                if ($item['weight'] <= 0) continue;

                $product = $products[$item['product_id']] ?? null;
                if (!$product) continue;

                $productNames[] = $product->product_name;

                ClientListOrder::create([
                    'client_list_order_client_id' => $client->id,
                    'client_list_order_store_id' => $store->id,
                    'client_list_order_id' => $order->id,
                    'client_list_order_product_id' => $product->id,
                    'client_list_order_weight' => $item['weight'],
                    'client_list_order_registration_date' => now(),
                    'client_list_order_value' => $totalValue,
                ]);

                if ($validated['direction'] === 'selling') {
                    $product->product_weight = bcsub((string)$product->product_weight, (string)$item['weight'], 4);
                    if (bccomp((string)$product->product_weight, '0', 4) <= 0) {
                        $product->product_state = 'Out of Stock';
                    }
                } else {
                    $product->product_weight = bcadd((string)$product->product_weight, (string)$item['weight'], 4);
                    $product->product_state = 'In Stock';
                }
                $product->save();
            }
        }

        // Build description from products + direction + status
        $directionLabel = $validated['direction'] === 'buying' ? __('messages.desc_bought') : __('messages.desc_sold');
        if (bccomp($remaining, '0', 4) > 0) {
            $statusLabel = $amountPaid > 0 ? __('messages.desc_partial') : __('messages.desc_outstanding');
        } else {
            $statusLabel = __('messages.desc_settled');
        }
        $productStr = $productNames ? implode(', ', array_slice($productNames, 0, 4)) : '';
        $description = $directionLabel . ($productStr ? ' – ' . $productStr : '') . ' – ' . $statusLabel;

        // Create the main transaction
        $this->createTransaction([
            'transaction_client_id' => $client->id,
            'transaction_store_id' => $store->id,
            'transaction_client_order_id' => $order->id,
            'transaction_transaction' => $txType,
            'transaction_amount' => $totalValue,
            'transaction_state' => $txState,
            'transaction_registration_date' => now(),
            'transaction_description' => $description,
        ]);

        // Update client_state based on direction + remaining
        if (bccomp($remaining, '0', 4) > 0) {
            if ($validated['direction'] === 'buying') {
                $state->client_state_amount = bcadd((string)$state->client_state_amount, $remaining, 4);
            } else {
                $state->client_state_amount = bcsub((string)$state->client_state_amount, $remaining, 4);
            }
        }
        $state->client_state_state = $this->computeState($state->client_state_amount);
        $state->client_state_last_transaction_date = now();
        $state->save();

        $msg = 'Transaction recorded. Total: $' . number_format($totalValue, 2);
        if ($amountPaid > 0) $msg .= ' | Paid now: $' . number_format($amountPaid, 2);
        if (bccomp($remaining, '0', 4) > 0) {
            $who = $validated['direction'] === 'buying' ? 'You owe client' : 'Client owes you';
            $msg .= ' | Remaining: $' . number_format((float)$remaining, 2) . ' (' . $who . ')';
        }

        return redirect()->route('customers.show', $client->id)
            ->with('status', $msg);
    }

    public function store(Request $request, $customerId)
    {
        $user = Auth::user();
        $storeIds = Store::where('store_user_id', $user->id)->pluck('id');

        $client = Client::whereHas('states', function ($q) use ($storeIds) {
            $q->whereIn('client_state_store_id', $storeIds);
        })->findOrFail($customerId);

        $type = $request->input('type');

        $store = Store::where('store_user_id', $user->id)
            ->where('store_active', true)
            ->first();

        if (!$store) {
            return back()->withErrors(['error' => 'No active store found.']);
        }

        $state = ClientState::firstOrNew(
            [
                'client_state_client_id' => $client->id,
                'client_state_store_id' => $store->id,
            ],
            [
                'client_state_amount' => 0,
                'client_state_state' => 'Settled',
                'client_state_last_transaction_date' => now(),
            ]
        );

        if (!in_array($type, ['Selling', 'Buying', 'Paying', 'Retriving', 'Settle'])) {
            return back()->withErrors(['error' => 'Invalid transaction type.']);
        }

        switch ($type) {
            case 'Selling':
                return $this->handleDebt($request, $client, $store, $state);
            case 'Paying':
                return $this->handlePayment($request, $client, $store, $state);
            case 'Buying':
                return $this->handlePurchase($request, $client, $store, $state);
            case 'Retriving':
                return $this->handleDelivery($request, $client, $store, $state);
            case 'Settle':
                return $this->handleSettlement($request, $client, $store, $state);
        }

        return redirect()->route('customers.show', $client->id)
            ->with('status', 'Transaction recorded.');
    }

    private function handleDebt(Request $request, Client $client, Store $store, ClientState $state)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:500'],
            'product_id' => ['nullable', 'exists:product,id'],
            'item_weight' => ['nullable', 'numeric', 'min:0'],
        ]);

        $order = ClientOrder::create([
            'client_order_client_id' => $client->id,
            'client_order_store_id' => $store->id,
            'client_order_value' => $validated['amount'],
            'client_order_state' => 'Debit',
            'client_order_registration_date' => now(),
            'client_order_date_due' => now(),
        ]);

        if ($validated['product_id'] ?? null) {
            ClientListOrder::create([
                'client_list_order_client_id' => $client->id,
                'client_list_order_store_id' => $store->id,
                'client_list_order_id' => $order->id,
                'client_list_order_product_id' => $validated['product_id'],
                'client_list_order_weight' => $validated['item_weight'] ?? 0,
                'client_list_order_registration_date' => now(),
                'client_list_order_value' => $validated['amount'],
            ]);
        }

        $this->createTransaction([
            'transaction_client_id' => $client->id,
            'transaction_store_id' => $store->id,
            'transaction_client_order_id' => $order->id,
            'transaction_transaction' => 'Buying',
            'transaction_amount' => $validated['amount'],
            'transaction_state' => 'Debit',
            'transaction_registration_date' => now(),
        ]);

        $state->client_state_amount = bcsub((string)$state->client_state_amount, (string)$validated['amount'], 4);
        $state->client_state_state = $this->computeState($state->client_state_amount);
        $state->client_state_last_transaction_date = now();
        $state->save();

        return redirect()->route('customers.show', $client->id)
            ->with('status', 'Debt of $' . number_format($validated['amount'], 2) . ' registered.');
    }

    private function handlePayment(Request $request, Client $client, Store $store, ClientState $state)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        if ($state->client_state_state !== 'Debit') {
            return back()->withErrors(['error' => 'Customer has no outstanding debt.']);
        }

        if ($validated['amount'] > abs($state->client_state_amount)) {
            return back()->withErrors(['amount' => 'Payment exceeds current debt of $' . number_format(abs($state->client_state_amount), 2) . '.']);
        }

        $order = ClientOrder::where('client_order_client_id', $client->id)
            ->where('client_order_store_id', $store->id)
            ->where('client_order_state', 'Debit')
            ->latest('client_order_registration_date')
            ->first();

        $this->createTransaction([
            'transaction_client_id' => $client->id,
            'transaction_store_id' => $store->id,
            'transaction_client_order_id' => $order?->id,
            'transaction_transaction' => 'Paying',
            'transaction_amount' => $validated['amount'],
            'transaction_state' => 'Settled',
            'transaction_registration_date' => now(),
        ]);

        $newAmount = bcadd((string)$state->client_state_amount, (string)$validated['amount'], 4);

        $state->client_state_amount = $newAmount;
        $state->client_state_state = $this->computeState($newAmount);
        if (bccomp($newAmount, '0', 4) >= 0 && $order) {
            $order->update(['client_order_state' => 'Settled']);
        }
        $state->client_state_last_transaction_date = now();
        $state->save();

        return redirect()->route('customers.show', $client->id)
            ->with('status', 'Payment of $' . number_format($validated['amount'], 2) . ' registered.');
    }

    private function handlePurchase(Request $request, Client $client, Store $store, ClientState $state)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'product_id' => ['nullable', 'exists:product,id'],
            'item_weight' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $order = ClientOrder::create([
            'client_order_client_id' => $client->id,
            'client_order_store_id' => $store->id,
            'client_order_value' => $validated['amount'],
            'client_order_state' => 'Favor',
            'client_order_registration_date' => now(),
            'client_order_date_due' => now(),
        ]);

        if ($validated['product_id'] ?? null) {
            ClientListOrder::create([
                'client_list_order_client_id' => $client->id,
                'client_list_order_store_id' => $store->id,
                'client_list_order_id' => $order->id,
                'client_list_order_product_id' => $validated['product_id'],
                'client_list_order_weight' => $validated['item_weight'] ?? 0,
                'client_list_order_registration_date' => now(),
                'client_list_order_value' => $validated['amount'],
            ]);
        }

        $this->createTransaction([
            'transaction_client_id' => $client->id,
            'transaction_store_id' => $store->id,
            'transaction_client_order_id' => $order->id,
            'transaction_transaction' => 'Selling',
            'transaction_amount' => $validated['amount'],
            'transaction_state' => 'Favor',
            'transaction_registration_date' => now(),
        ]);

        $state->client_state_amount = bcadd((string)$state->client_state_amount, (string)$validated['amount'], 4);
        $state->client_state_state = $this->computeState($state->client_state_amount);
        $state->client_state_last_transaction_date = now();
        $state->save();

        return redirect()->route('customers.show', $client->id)
            ->with('status', 'Purchase of $' . number_format($validated['amount'], 2) . ' recorded.');
    }

    private function handleDelivery(Request $request, Client $client, Store $store, ClientState $state)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        if ($state->client_state_state !== 'Favor') {
            return back()->withErrors(['error' => 'Customer has no favorable balance.']);
        }

        if ($validated['amount'] > $state->client_state_amount) {
            return back()->withErrors(['amount' => 'Delivery exceeds current balance of $' . number_format($state->client_state_amount, 2) . '.']);
        }

        $order = ClientOrder::where('client_order_client_id', $client->id)
            ->where('client_order_store_id', $store->id)
            ->where('client_order_state', 'Favor')
            ->latest('client_order_registration_date')
            ->first();

        $this->createTransaction([
            'transaction_client_id' => $client->id,
            'transaction_store_id' => $store->id,
            'transaction_client_order_id' => $order?->id,
            'transaction_transaction' => 'Retriving',
            'transaction_amount' => $validated['amount'],
            'transaction_state' => 'Settled',
            'transaction_registration_date' => now(),
        ]);

        $newAmount = bcsub((string)$state->client_state_amount, (string)$validated['amount'], 4);

        $state->client_state_amount = $newAmount;
        $state->client_state_state = $this->computeState($newAmount);
        if (bccomp($newAmount, '0', 4) <= 0 && $order) {
            $order->update(['client_order_state' => 'Settled']);
        }
        $state->client_state_last_transaction_date = now();
        $state->save();

        return redirect()->route('customers.show', $client->id)
            ->with('status', 'Money delivery of $' . number_format($validated['amount'], 2) . ' recorded.');
    }

    private function handleSettlement(Request $request, Client $client, Store $store, ClientState $state)
    {
        if (bccomp((string)$state->client_state_amount, '0', 4) === 0) {
            return back()->withErrors(['error' => 'Account is already settled.']);
        }

        $prevAmount = $state->client_state_amount;
        $prevState = $state->client_state_state;

        ClientOrder::where('client_order_client_id', $client->id)
            ->where('client_order_store_id', $store->id)
            ->where('client_order_state', $prevState)
            ->update(['client_order_state' => 'Settled']);

        $this->createTransaction([
            'transaction_client_id' => $client->id,
            'transaction_store_id' => $store->id,
            'transaction_client_order_id' => null,
            'transaction_transaction' => 'Settle',
            'transaction_amount' => abs($prevAmount),
            'transaction_state' => 'Settled',
            'transaction_registration_date' => now(),
        ]);

        $state->client_state_state = 'Settled';
        $state->client_state_amount = 0;
        $state->client_state_last_transaction_date = now();
        $state->save();

        return redirect()->route('customers.show', $client->id)
            ->with('status', 'Account settled. Previous balance: $' . number_format(abs($prevAmount), 2));
    }
}
