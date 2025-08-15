<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RecurringExpense extends Model
{
    protected $fillable = [
        'user_id', 'description', 'amount', 'category_id', 'frequency', 'next_occurrence'
    ];

    public static function calculateNextOccurrence($date, $frequency)
    {
        $date = Carbon::parse($date);
        return match ($frequency) {
            'daily' => $date->addDay(),
            'weekly' => $date->addWeek(),
            'monthly' => $date->addMonth(),
        };
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
