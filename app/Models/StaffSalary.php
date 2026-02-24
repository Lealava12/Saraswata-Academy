<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffSalary extends Model
{
    protected $table = 'staff_salaries';
    protected $fillable = [
        'staff_id',
        'amount',
        'payment_month',
        'payment_date',
        'slug',
        'is_active',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->slug = $m->slug ?: \Str::slug('ssal-' . uniqid()));
    }
}
