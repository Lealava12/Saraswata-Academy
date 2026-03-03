<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherSalary extends Model
{
    protected $table = 'teacher_salaries';
    protected $fillable = [
        'teacher_id',
        'amount',
        'payment_month',
        'payment_date',
        'class_count',
        'class_id',
        'slug',
        'is_active',
        'breakdown',
    ];

    protected $casts = [
        'breakdown' => 'array',
        'payment_date' => 'date',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->slug = $m->slug ?: \Str::slug('tsal-' . uniqid()));
    }
}
