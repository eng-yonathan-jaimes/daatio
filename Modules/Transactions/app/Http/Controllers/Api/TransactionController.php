<?php

namespace Modules\Transactions\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Transactions\app\Models\Transaction;

class TransactionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $transactions = Transaction::with(['client', 'store', 'clientOrder'])->paginate($request->query('per_page', 15));

        return response()->json($transactions);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'transaction_client_id' => ['required', 'integer', 'min:1'],
            'transaction_store_id' => ['required', 'integer', 'min:1'],
            'transaction_client_order_id' => ['sometimes', 'integer', 'min:1'],
            'transaction_transaction' => ['required', 'string', 'in:Selling,Buying,Paying,Retriving'],
            'transaction_amount' => ['required', 'numeric', 'min:0'],
            'transaction_state' => ['required', 'string', 'in:Favor,Debit,Settled'],
        ]);

        $data['transaction_registration_date'] = now();

        $transaction = Transaction::create($data);

        return response()->json(['data' => $transaction->load(['client', 'store', 'clientOrder'])], 201);
    }

    public function show(Transaction $transaction): JsonResponse
    {
        return response()->json(['data' => $transaction->load(['client', 'store', 'clientOrder'])]);
    }

    public function update(Request $request, Transaction $transaction): JsonResponse
    {
        $data = $request->validate([
            'transaction_client_id' => ['sometimes', 'integer', 'min:1'],
            'transaction_store_id' => ['sometimes', 'integer', 'min:1'],
            'transaction_client_order_id' => ['sometimes', 'integer', 'min:1'],
            'transaction_transaction' => ['sometimes', 'string', 'in:Selling,Buying,Paying,Retriving'],
            'transaction_amount' => ['sometimes', 'numeric', 'min:0'],
            'transaction_state' => ['sometimes', 'string', 'in:Favor,Debit,Settled'],
        ]);

        $transaction->update($data);

        return response()->json(['data' => $transaction->load(['client', 'store', 'clientOrder'])]);
    }

    public function destroy(Transaction $transaction): JsonResponse
    {
        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted successfully.']);
    }
}
