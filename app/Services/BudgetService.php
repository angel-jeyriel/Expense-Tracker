<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Transaction;
use App\Notifications\BudgetExceededNotification;

class BudgetService
{
    public static function checkBudgets($userId): void
    {
        $budgets = Budget::where('user_id', $userId)->get();

        foreach ($budgets as $budget) {
            $spent = Transaction::forUser()
                ->where('category_id', $budget->category_id)
                ->whereBetween('transaction_date', [$budget->start_date, $budget->end_date])
                ->sum('amount');

            if ($spent > $budget->amount) {
                $budget->user->notify(new BudgetExceededNotification($budget, $spent));
            }
        }
    }
}
