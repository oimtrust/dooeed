<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'user' => new UserResource($this->resource['user']),
            'accounts' => $this->resource['accounts']->map(fn (object $account): array => [
                'id' => $account->id, 'name' => $account->name,
                'currency' => $account->currency, 'current_balance' => (string) $account->current_balance,
            ]),
            'balances' => $this->resource['balances']->map(fn (object $balance): array => [
                'currency' => $balance->currency, 'total' => (string) $balance->total,
            ]),
            'outstanding_debts' => (string) $this->resource['debts'],
            'investments' => $this->resource['investments']->map(fn (object $investment): array => [
                'id' => $investment->id, 'name' => $investment->name,
                'platform' => $investment->platform, 'assets_count' => (int) $investment->assets_count,
            ]),
            'transactions' => $this->resource['transactions']->map(fn (object $transaction): array => [
                'id' => $transaction->id, 'transaction_date' => $transaction->transaction_date,
                'account_name' => $transaction->account_name, 'currency' => $transaction->currency,
                'type' => $transaction->type, 'amount' => (string) $transaction->amount,
                'description' => $transaction->description,
            ]),
        ];
    }
}
