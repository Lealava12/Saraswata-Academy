<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'name',
        'mobile',
        'address',
        'joining_date',
        'slug',
        'is_active',
    ];


    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects');
    }
    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'teacher_classes', 'teacher_id', 'class_id')->withPivot('amount')->withTimestamps();
    }
    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class);
    }
    public function salaries()
    {
        return $this->hasMany(TeacherSalary::class);
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
