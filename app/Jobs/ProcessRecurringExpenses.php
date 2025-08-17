<?php

namespace App\Jobs;

use App\Models\RecurringExpense;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessRecurringExpenses implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $today = Carbon::today();
        $recurringExpenses = RecurringExpense::where('next_occurrence', $today)->get();

        foreach ($recurringExpenses as $expense) {
            Transaction::create([
                'user_id' => $expense->user_id,
                'description' => $expense->description,
                'amount' => $expense->amount,
                'category_id' => $expense->category_id,
                'transaction_date' => $today,
            ]);

            $expense->update([
                'next_occurrence' => RecurringExpense::calculateNextOccurrence($expense->next_occurrence, $expense->frequency),
            ]);
        }
    }
}
