<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\RecurringExpense;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function afterCreate(): void
    {
        $data = $this->record->toArray();

        if (!empty($data['is_recurring']) && $data['is_recurring'] == 1) {
            RecurringExpense::create([
                'user_id' => $data['user_id'],
                'description' => $data['description'],
                'amount' => $data['amount'],
                'category_id' => $data['category_id'],
                'frequency' => $data['frequency'],
                'next_occurrence' => RecurringExpense::calculateNextOccurrence($data['transaction_date'], $data['frequency']),
            ]);
        } else {
            dd($data['amount']);
        }
    }
}
