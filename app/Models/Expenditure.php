<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expenditure extends Model
{
    protected $fillable = [
        'title',
        'amount',
        'expense_date',
        'description',
        'slug',
        'is_active',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->slug = $m->slug ?: \Str::slug('exp-' . uniqid()));
    }
}
