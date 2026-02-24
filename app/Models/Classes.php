<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $table = 'classes';
    protected $fillable = ['name', 'board_id', 'monthly_fee', 'slug', 'is_active'];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }
    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'class_id');
    }
    public function exams()
    {
        return $this->hasMany(Exam::class, 'class_id');
    }
    public function fees()
    {
        return $this->hasMany(StudentFee::class, 'class_id');
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
