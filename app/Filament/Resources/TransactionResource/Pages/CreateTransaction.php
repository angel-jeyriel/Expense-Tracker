<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\RecurringExpense;
use App\Services\BudgetService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function afterCreate(): void
    {
        $data = $this->record->toArray();
        $isRecurring = $this->data['is_recurring'] ?? 0;
        $frequency = $this->data['frequency'] ?? null;

        if ($isRecurring && $frequency) {
            RecurringExpense::create([
                'user_id' => $data['user_id'],
                'description' => $data['description'],
                'amount' => $data['amount'],
                'category_id' => $data['category_id'],
                'frequency' => $frequency,
                'next_occurrence' => RecurringExpense::calculateNextOccurrence($data['transaction_date'], $frequency),
            ]);
        }

        // Budget check
        BudgetService::checkBudgets($data['user_id']);
    }
}
