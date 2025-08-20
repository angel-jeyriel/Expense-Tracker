<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\BudgetExceededNotification;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Events\DatabaseNotificationsSent;

class BudgetService
{
    public static function checkBudgets(int $userId): void
    {
        $user = User::findOrFail($userId);
        $budgets = Budget::where('user_id', $userId)->get();

        foreach ($budgets as $budget) {
            $spent = Transaction::forUser()
                ->where('category_id', $budget->category_id)
                ->whereBetween('transaction_date', [$budget->start_date, $budget->end_date])
                ->sum('amount');

            if ($spent > $budget->amount) {
                // Filament database notification
                FilamentNotification::make()
                    ->title('Budget Exceeded')
                    ->body("You've exceeded your budget for {$budget->category->name}: spent {$spent}, limit {$budget->amount}.")
                    ->danger()
                    ->actions([
                        Action::make('markAsUnread')
                            ->button()
                            ->markAsUnread(),
                    ])->sendToDatabase($user);

                // the traditional notify
                $user->notify(new BudgetExceededNotification($budget, $spent));
            }
        }
    }
}
