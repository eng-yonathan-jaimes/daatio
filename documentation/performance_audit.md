# Performance Audit — daatio

**Generated:** 2026-06-09
**Scope:** `app/Http/Controllers/`, `Modules/*/app/Http/Controllers/`, `Modules/*/app/Models/`

---

## 1. Modules/Transactions/app/Http/Controllers/TransactionsController.php

### 1.1 `quickStore(Request $request)` (line 79–245) — **CRITICAL**

#### Current Big O: O(n^2) queries (2N individual `Product::find` queries)

**Reasoning:**
- **First loop** (lines 133–146, stock validation): iterates over `$validated['items']` and calls `Product::find($item['product_id'])` for each item. Each call is 1 DB query → **N queries**.
- **Second loop** (lines 169–199, line-item creation + stock update): iterates over the same items **again** and calls `Product::find($item['product_id'])` again for each item → **another N queries**.
- Each `$product->save()` inside the second loop is also 1 UPDATE query → **N more queries**.
- **Total: ~3N DB round-trips** for N items. A 10-item order = ~30 queries.

#### Optimized version:

```php
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
        ['client_state_client_id' => $client->id, 'client_state_store_id' => $store->id],
        ['client_state_amount' => 0, 'client_state_state' => 'Settled', 'client_state_last_transaction_date' => now()]
    );

    $txType = $validated['direction'] === 'buying' ? 'Selling' : 'Buying';

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

    // Validate stock using cached products (0 queries)
    if ($validated['direction'] === 'selling' && !empty($validated['items'])) {
        $weightByProduct = [];
        foreach ($validated['items'] as $item) {
            if (empty($item['product_id']) || empty($item['weight']) || $item['weight'] <= 0) continue;
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

    $productNames = [];
    if (!empty($validated['items'])) {
        foreach ($validated['items'] as $item) {
            if (empty($item['product_id']) || empty($item['weight']) || $item['weight'] <= 0) continue;

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

            // Update product stock in-place (still cached)
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

    $directionLabel = $validated['direction'] === 'buying' ? __('messages.desc_bought') : __('messages.desc_sold');
    if (bccomp($remaining, '0', 4) > 0) {
        $statusLabel = $amountPaid > 0 ? __('messages.desc_partial') : __('messages.desc_outstanding');
    } else {
        $statusLabel = __('messages.desc_settled');
    }
    $productStr = $productNames ? implode(', ', array_slice($productNames, 0, 4)) : '';
    $description = $directionLabel . ($productStr ? ' – ' . $productStr : '') . ' – ' . $statusLabel;

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
```

#### New Big O: O(1) queries for product fetching (1 bulk query + writes)

**Improvement:** Reduced from ~3N DB round-trips to 1 bulk SELECT + N UPDATEs (the UPDATEs are unavoidable). For a 10-item order: ~30 queries → ~11 queries.

---

### 1.2 `handlePayment(Request $request, ...)` (line 348) — **MEDIUM**

#### Current Big O: O(1) time (3 DB ops). No N+1 issue.

**Reasoning:** This method has no loops. It does 1 query for the latest Debit order, 1 INSERT (via `createTransaction`), 1 optional UPDATE (to settle the order), and 1 save on state. Fine as-is.

**Note:** `abs($state->client_state_amount)` on line 359 calls PHP's `abs()` on a decimal-cast value. Since `client_state_amount` is cast to `decimal:4`, this works correctly but the value is already a numeric string in some contexts. Consistency with `bccomp` would be better but is not a performance issue.

---

## 2. app/Http/Controllers/DashboardController.php

### 2.1 `index(Request $request)` (line 15–83) — **HIGH**

#### Current Big O: O(D) time, ~3 queries + 1 O(D) PHP iteration

**Reasoning:**
- `$debtState = ClientState::whereIn(...)->get()` fetches ALL debit state records into a PHP collection.
- `$debtState->sum(function($s) { return abs($s->client_state_amount); })` iterates the entire collection in PHP to sum absolute values.
- Two separate queries hit the same `client_state` table for Debit and Favor amounts; a single grouped query would serve both.
- `$totalPaid` is computed via database `sum()` — this is already optimal.

#### Optimized version:

```php
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
```

#### New Big O: O(1) time (2 DB-level aggregations, no PHP collection iteration)

**Improvement:** Eliminates O(D) PHP-side iteration over debit states (where D = number of debit records). The DB does the sum natively. Also collapses two separate `client_state` queries into one grouped query.

---

## 3. Modules/Clients/app/Http/Controllers/Api/ClientOrderController.php

### 3.1 `index()` (line 11) — **HIGH**

#### Current Big O: O(N) memory, no pagination

**Reasoning:** `ClientOrder::with('items.product')->get()` loads every client order with its items and nested products into memory. For a growing system, this will eventually exhaust memory or cause timeouts.

#### Optimized version:

```php
public function index(Request $request)
{
    return ClientOrder::with('items.product')
        ->orderBy('client_order_registration_date', 'desc')
        ->paginate($request->query('per_page', 25));
}
```

#### New Big O: O(P) memory where P = page size (constant upper bound)

---

## 4. Modules/Clients/app/Http/Controllers/Api/ClientController.php

### 4.1 `index()` (line 11) — **HIGH**

#### Current Big O: O(N) memory, no pagination

**Reasoning:** `Client::all()` loads every client into memory.

#### Optimized version:

```php
public function index(Request $request)
{
    return Client::orderBy('client_name')
        ->paginate($request->query('per_page', 25));
}
```

#### New Big O: O(P) memory

---

## 5. Modules/Clients/app/Http/Controllers/Api/ClientStateController.php

### 5.1 `index()` (line 11) — **HIGH**

#### Current Big O: O(N) memory, no pagination

**Reasoning:** `ClientState::all()` loads all rows.

#### Optimized version:

```php
public function index(Request $request)
{
    return ClientState::paginate($request->query('per_page', 50));
}
```

#### New Big O: O(P) memory

---

## 6. Modules/Clients/app/Http/Controllers/Api/ClientListOrderController.php

### 6.1 `index()` (line 11) — **HIGH**

#### Current Big O: O(N) memory, no pagination

**Reasoning:** `ClientListOrder::all()` loads all list-order line items.

#### Optimized version:

```php
public function index(Request $request)
{
    return ClientListOrder::paginate($request->query('per_page', 50));
}
```

#### New Big O: O(P) memory

---

## 7. Modules/Clients/app/Http/Controllers/Api/ClientOrderController.php (additional)

### 7.1 `store(Request $request)` (line 12) — **MEDIUM**

#### Current Big O: O(1) queries

**Reasoning:** `ClientOrder::create($request->all())` accepts raw request data without validation. Aside from obvious security concerns (mass-assignment with `$guarded`), any value could be injected. Not strictly a performance issue but worth flagging: the `$guarded` approach plus `$request->all()` bypasses attribute typing/casting safety.

#### Optimized version:

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'client_order_client_id' => ['required', 'exists:client,id'],
        'client_order_store_id' => ['required', 'exists:store,id'],
        'client_order_value' => ['required', 'numeric', 'min:0'],
        'client_order_state' => ['required', 'string'],
    ]);
    $validated['client_order_registration_date'] = now();
    $validated['client_order_date_due'] = now();
    return ClientOrder::create($validated);
}
```

#### New Big O: O(1) — same complexity, added safety

---

## 8. app/Http/Controllers/CustomerController.php

### 8.1 `show($id)` (line 91–115) — **MEDIUM**

#### Current Big O: O(1) query for main entity, O(T+O) for nested relations

**Reasoning:** The `findOrFail($id)` returns 1 client. The `with()` clauses on `transactions`, `states`, and `orders` are 3 eager-load queries. `orders` further loads `items.product` (2 more queries). Total: ~5 queries for a single customer detail view. This is acceptable for a detail-view page but could be reduced.

```php
// Current eager loading chain
->with([
    'states' => fn($q) use ($storeIds) { $q->whereIn('client_state_store_id', $storeIds); },
    'transactions' => fn($q) use ($storeIds) {
        $q->whereIn('transaction_store_id', $storeIds)->with('user')
          ->orderBy('transaction_registration_date', 'desc');
    },
    'orders' => fn($q) use ($storeIds) {
        $q->whereIn('client_order_store_id', $storeIds)->with('items.product')
          ->orderBy('client_order_registration_date', 'desc');
    },
])
```

This is already eager-loaded; no N+1 issues exist. However, if `orders` is not needed on every `show` call, consider lazy-loading it only when the view requests it.

**Verdict:** Already well-optimized for the use case. No change recommended.

---

### 8.2 `index(Request $request)` (line 14–37) — **LOW**

#### Current Big O: O(C) where C = matching customers

**Reasoning:** No `with()` is used on the customers query result. If the view iterates `$customers` and accesses each customer's states (e.g., `$customer->states`), it will trigger N+1 queries. However, the listing view may only show customer name/phone from the `client` table itself.

**If** the view accesses `$customer->states->first()` or similar for each row, add eager loading:

```php
$customers = $customers
    ->with(['states' => fn($q) use ($storeIds) {
        $q->whereIn('client_state_store_id', $storeIds);
    }])
    ->orderBy('client_name')
    ->get();
```

---

## 9. app/Http/Controllers/PrintController.php

### 9.1 `customerList(Request $request)` (line 49–75) — **LOW**

#### Current Big O: O(N) where N = matching clients, with eager-loaded states

**Reasoning:** Same pattern as `CustomerController::index()`. States are already eager-loaded. Acceptable.

---

### 9.2 `customerHistory($id)` (line 13–31) — **LOW**

**Reasoning:** Single-entity detail view with eager-loaded transactions and states. ~5 queries total. Acceptable for a print view.

---

## 10. app/Http/Controllers/Auth/ResetPasswordController.php

### 10.1 `reset(Request $request)` (line 22–56) — **MEDIUM**

#### Current Big O: O(1) time, ~3 DB queries

**Reasoning:** `Password::reset()` internally looks up the user by email and token. Then on line 39, `User::where('user_email', $request->email)->first()` performs a **second** lookup of the same user. This is a redundant query.

#### Optimized version:

Capture the user from the reset callback closure:

```php
public function reset(Request $request)
{
    $request->validate([
        'token' => ['required'],
        'email' => ['required', 'email'],
        'password' => ['required', 'confirmed'],
    ]);

    $resetUser = null;

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) use (&$resetUser) {
            $user->forceFill([
                'user_password' => Hash::make($password),
            ])->save();
            $resetUser = $user;
        }
    );

    if ($resetUser) {
        UserRecoveryHistory::create([
            'user_recovery_history_intent_date' => now(),
            'user_recovery_history_recovery_answered' => $status === Password::PASSWORD_RESET ? 'yes' : 'no',
            'user_recovery_history_recovered_success' => $status === Password::PASSWORD_RESET,
            'user_recovery_history_method_used' => 'email',
            'user_recovery_history_ip' => $request->ip(),
            'user_recovery_history_user_id' => $resetUser->id,
            'user_recovery_history_decive' => $request->userAgent() ?? 'unknown',
        ]);
    }

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => __($status)]);
}
```

#### New Big O: O(1) time, ~2 DB queries (eliminates duplicate `User::where('user_email', ...)` lookup)

---

## 11. app/Http/Controllers/Auth/ForgotPasswordController.php

### 11.1 `sendResetLinkEmail(Request $request)` (line 18–45) — **MEDIUM**

**Reasoning:** Same pattern as ResetPasswordController. `Password::sendResetLink()` internally looks up the user, and then line 24 does `User::where('user_email', $request->email)->first()` for a redundant second lookup. However, `Password::sendResetLink()` does not expose the user object via a callback (unlike `Password::reset()`), so the user cannot be captured without a facade override. The duplicate lookup is acceptable here since the `sendResetLink` broker does not provide a callback signature that exposes the user.

**Verdict:** No clean fix without overriding the password broker. The lookup is indexed on `user_email` (assumed), so cost is negligible.

---

## 12. Modules/Products/app/Http/Controllers/ProductsController.php

### 12.1 Repeated `Store` pluck queries — **LOW**

#### Current Big O: O(1) redundant queries

**Reasoning:** `edit()` (line 59), `update()` (line 67), and `destroy()` (line 92) each independently call `Store::where('store_user_id', Auth::id())->pluck('id')`. In a request lifecycle where multiple of these are called, this is 3 nearly-identical queries.

#### Optimized version:

Extract a private helper:

```php
private function userStoreIds(): array
{
    return Store::where('store_user_id', Auth::id())->pluck('id')->all();
}

public function edit($id)
{
    $product = Product::whereIn('product_store_id', $this->userStoreIds())->findOrFail($id);
    return view('products.edit', compact('product'));
}
```

#### New Big O: O(1) — same per-call, but reduces code duplication.

---

## 13. app/Http/Controllers/RegisterController.php

### 13.1 `register(Request $request)` (line 21–85) — **LOW**

**Reasoning:** Calls `now()` 5 times. Each call creates a Carbon instance. Could use a single `$now = now()` variable.

#### Optimized version:

```php
public function register(Request $request)
{
    $validated = $request->validate([...]);

    $fullPhone = $validated['country_code'] . ' ' . $validated['phone'];

    $existingPhone = User::where('user_phone_number', $fullPhone)->exists();
    if ($existingPhone) {
        return back()->withErrors(['phone' => 'This phone number is already registered.'])->withInput();
    }

    $now = now();

    $user = User::create([
        'tenant_id' => 1,
        'user_name' => $validated['first_name'],
        'user_lastName' => $validated['last_name'],
        'user_email' => $validated['email'],
        'user_phone_number' => $fullPhone,
        'user_password' => Hash::make($validated['password']),
        'user_access' => 'user',
        'user_creation' => $now,
        'user_update_date' => $now,
    ]);

    $user->generateEmailVerificationCode();
    $user->generatePhoneVerificationCode();

    $trialPlan = Subscription::where('subscription_type', 'Trial')
        ->where('subscription_enabled', true)
        ->first();

    if ($trialPlan) {
        UserSubscription::create([
            'user_subscription_user_id' => $user->id,
            'user_subscription_subscription_id' => $trialPlan->id,
            'user_subscription_start_date' => $now,
            'user_subscription_end_date' => $now->copy()->addDays($trialPlan->subscription_days),
            'user_subscription_value' => $trialPlan->subscription_value,
            'user_subscription_status' => 'Active',
            'user_subscription_trial_ends_date' => $now->copy()->addDays($trialPlan->subscription_days),
        ]);
    }

    Store::create([
        'store_user_id' => $user->id,
        'store_name' => $validated['first_name'] . "'s Store",
        'store_active' => true,
        'store_update_date' => $now,
        'store_address' => '',
        'store_type_id' => 1,
        'store_location' => 'Physical',
        'store_registration_date' => $now,
    ]);

    auth()->login($user);
    $request->session()->regenerate();

    return redirect()->route('verification.notice');
}
```

#### New Big O: O(1) — micro-optimization (saves 4 Carbon instantiations)

---

## 14. Modules/Users/app/Http/Controllers/Api/AuthController.php

### 14.1 `userPayload(User $user)` (line 108–121) — **LOW**

#### Current Big O: O(1)

**Reasoning:** Manually builds an array from user attributes. Could use `$user->only([...])` or `$user->makeHidden([...])->toArray()` for cleaner code, but no measurable performance difference at this scale.

---

## 15. Summary Table

| # | File | Method | Issue | Impact | Queries Before | Queries After |
|---|------|--------|-------|--------|---------------|--------------|
| 1 | TransactionsController | `quickStore()` | Double N+1 product fetch loop | **CRITICAL** | ~3N | ~1 + N |
| 2 | DashboardController | `index()` | Collection `sum()` instead of DB aggregation | **HIGH** | 2 debits + O(D) PHP | 1 grouped |
| 3 | ClientOrderController (Api) | `index()` | No pagination | **HIGH** | 3 (all rows) | 3 (per page) |
| 4 | ClientController (Api) | `index()` | No pagination | **HIGH** | 1 (all rows) | 1 (per page) |
| 5 | ClientStateController (Api) | `index()` | No pagination | **HIGH** | 1 (all rows) | 1 (per page) |
| 6 | ClientListOrderController (Api) | `index()` | No pagination | **HIGH** | 1 (all rows) | 1 (per page) |
| 7 | ResetPasswordController | `reset()` | Duplicate user lookup | **MEDIUM** | 3 | 2 |
| 8 | ProductsController | `edit/update/destroy` | Repeated store-pluck query | **LOW** | 1 each | 1 each (de-duplicated) |
| 9 | RegisterController | `register()` | Multiple `now()` calls | **LOW** | 6 | 6 (micro) |

---

## 16. General Recommendations

### Database Indexes (Inferred from Query Patterns)

The following columns are used in WHERE clauses across the codebase and should have indexes:

| Table | Column(s) | Used in |
|-------|-----------|---------|
| `store` | `store_user_id` | Nearly every controller |
| `client_state` | `client_state_store_id`, `client_state_state` | Dashboard, Transactions |
| `client_state` | `client_state_client_id`, `client_state_store_id` | Transactions (composite unique) |
| `transaction` | `transaction_store_id`, `transaction_transaction` | Dashboard, Transactions listing |
| `transaction` | `transaction_client_id` | Client detail page |
| `user_subscription` | `user_subscription_user_id`, `user_subscription_status` | Dashboard, Subscription |
| `client` | `client_name`, `client_last_name`, `client_phone_number` | Full-text search (consider `FULLTEXT` index) |
| `user` | `user_email` | Login, ForgotPassword, ResetPassword |
| `user` | `user_phone_number` | Register |
| `subscription` | `subscription_type`, `subscription_enabled` | Register (composite) |

### Eager Loading Patterns

The codebase consistently uses `with()` for nested relations, which is good. The pattern `whereHas` + `with` with identical `whereIn` filters is repeated in multiple controllers:

```php
// Pattern repeated in: CustomerController, PrintController, DashboardController, TransactionsController
Store::where('store_user_id', $user->id)->pluck('id');
ClientState::whereIn('client_state_store_id', $storeIds);
```

Consider extracting this to a helper or a method on the User model (e.g., `$user->storeIds()` or `$user->activeStoreIds()`).

### bcmath Usage

`bccomp`, `bcadd`, `bcsub` are used correctly throughout `TransactionsController`. This is appropriate for financial precision with 4 decimal places. No simplifications recommended—removing bcmath would introduce floating-point rounding errors.

### `client_stores_ids` JSON Column

The `Client::client_stores_ids` column is cast to `array` in the model but also manually set via `json_encode()` in `CustomerController::store()` (line 72). The manual `json_encode` is unnecessary with the model cast. However, this does not affect performance.

### Sanctum Token Lookup

In `AuthController::logout()` (line 86), `PersonalAccessToken::findToken()` is used as a fallback when `currentAccessToken()` is null. This is a safety net but adds a query. If `currentAccessToken()` reliably returns the token (it should for authenticated requests), the fallback is dead code and adds an unnecessary query in the null case.

---

## 17. Audit Scope Notes

- Only files with **meaningful logic** were analyzed. Simple passthrough methods (return view, redirect, simple assignment) were excluded.
- Model relationship definitions were reviewed but are standard Eloquent patterns with no performance concerns.
- This audit does **not** include view files, config files, routes, migrations, or seeders.
- Memory complexity (space) is O(1) for all reviewed methods except API `index()` methods without pagination, which are O(N).
