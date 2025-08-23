<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'description', 'amount', 'category_id', 'transaction_date', 'is_recurring',
        'frequency'
    ];

    protected $casts = [
        'is_recurring' => 'boolean',
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function scopeForUser($query)
    {
        return $query->where('user_id', auth()->id());
    }
}
