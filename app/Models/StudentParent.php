<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentParent extends Model
{
    protected $table = 'student_parents';
    protected $fillable = [
        'student_id',
        'father_name',
        'father_mobile',
        'mother_name',
        'mother_mobile',
        'address',
        'slug',
        'is_active',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->slug = $m->slug ?: \Str::slug('sp-' . uniqid()));
    }
}
