<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $fillable = [
        'name',
        'role',
        'mobile',
        'monthly_salary',
        'joining_date',
        'slug',
        'is_active',
        
    ];
protected $casts = [
        'joining_date' => 'date',
        'is_active' => 'boolean',
        'monthly_salary' => 'decimal:2',
    ];

    public function salaries()
    {
        return $this->hasMany(StaffSalary::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->slug = $m->slug ?: \Str::slug($m->name . '-' . uniqid()));
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', 1);
    }
}
