<?php

namespace App\Domains\Admin\Queries;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class GetUserSummary
{
    /** @return array<string, mixed> */
    public function execute(User $user): array
    {
        $accounts = DB::table('accounts')->where('user_id', $user->id)->orderBy('name')->get();
        $balances = DB::table('accounts')->where('user_id', $user->id)
            ->select('currency')->selectRaw('SUM(current_balance) as total')->groupBy('currency')->get();
        $debts = DB::table('debts')->where('user_id', $user->id)->where('type', 'debt')
            ->whereIn('status', ['active', 'overdue'])->sum('outstanding_amount');
        $investments = DB::table('investment_accounts')->where('user_id', $user->id)
            ->select('investment_accounts.*')->selectSub(DB::table('investment_assets')->selectRaw('count(*)')
            ->whereColumn('investment_account_id', 'investment_accounts.id'), 'assets_count')->get();
        $transactions = DB::table('transactions')->where('transactions.user_id', $user->id)
            ->join('accounts', 'accounts.id', '=', 'transactions.account_id')
            ->select('transactions.*', 'accounts.name as account_name', 'accounts.currency')
            ->orderByDesc('transaction_date')->orderByDesc('transactions.created_at')->orderByDesc('transactions.id')->limit(20)->get();

        return compact('user', 'accounts', 'balances', 'debts', 'investments', 'transactions');
    }
}
