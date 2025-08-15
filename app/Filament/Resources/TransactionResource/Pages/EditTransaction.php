<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\RecurringExpense;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $data = $this->record->toArray();

        if (!empty($data['is_recurring']) && $data['is_recurring'] == 1) {
            // Check if there's already a recurring expense for this transaction's details
            $exists = RecurringExpense::where('description', $data['description'])
                ->where('user_id', $data['user_id'])
                ->where('amount', $data['amount'])
                ->where('category_id', $data['category_id'])
                ->where('frequency', $data['frequency'])
                ->exists();

            if (!$exists) {
                RecurringExpense::create([
                    'user_id' => $data['user_id'],
                    'description' => $data['description'],
                    'amount' => $data['amount'],
                    'category_id' => $data['category_id'],
                    'frequency' => $data['frequency'],
                    'next_occurrence' => RecurringExpense::calculateNextOccurrence(
                        $data['transaction_date'],
                        $data['frequency']
                    ),
                ]);
            }
        }
    }
}
