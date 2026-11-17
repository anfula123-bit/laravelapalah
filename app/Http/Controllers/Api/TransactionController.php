<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    /**
     * List every transaction.
     */
    public function index(): JsonResponse
    {
        $transactions = Transaction::query()->orderBy('id')->get();

        return response()->json([
            'success' => true,
            'message' => 'List of transactions',
            'data' => $transactions->map(fn (Transaction $transaction) => $this->detail($transaction))->all(),
        ]);
    }

    /**
     * Store a new transaction.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'transaction_code' => ['required', 'string', 'max:50', 'unique:transactions,transaction_code'],
            'transaction_date' => ['required', 'date'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:50'],
        ]);

        $transaction = Transaction::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Create transaction success',
            'data' => $this->detail($transaction),
        ]);
    }

    /**
     * Show a single transaction.
     */
    public function show(int $id): JsonResponse
    {
        $transaction = Transaction::find($id);

        if ($transaction === null) {
            return $this->notFound('Transaction not found');
        }

        return response()->json([
            'success' => true,
            'message' => 'Get transaction by ID',
            'data' => [$this->detail($transaction)],
        ]);
    }

    /**
     * Update an existing transaction.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $transaction = Transaction::find($id);

        if ($transaction === null) {
            return $this->notFound('Transaction not found');
        }

        $validated = $request->validate([
            'transaction_code' => ['sometimes', 'required', 'string', 'max:50', Rule::unique('transactions', 'transaction_code')->ignore($transaction->id)],
            'transaction_date' => ['sometimes', 'required', 'date'],
            'total_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'payment_method' => ['sometimes', 'nullable', 'string', 'max:50'],
        ]);

        $transaction->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Transaction updated successfully',
            'data' => $this->detail($transaction),
        ]);
    }

    /**
     * Delete a transaction.
     */
    public function destroy(int $id): JsonResponse
    {
        $transaction = Transaction::find($id);

        if ($transaction === null) {
            return $this->notFound('Transaction not found');
        }

        $transaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaction deleted successfully',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function detail(Transaction $transaction): array
    {
        return [
            'id' => $transaction->id,
            'transaction_code' => $transaction->transaction_code,
            'transaction_date' => $transaction->transaction_date?->format('Y-m-d H:i:s'),
            'total_amount' => $transaction->total_amount,
            'payment_method' => $transaction->payment_method,
        ];
    }
}
