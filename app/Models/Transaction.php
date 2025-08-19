<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['user_id', 'description', 'amount', 'category_id', 'transaction_date'];

    protected $casts = [
        'is_recurring' => 'boolean',
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
