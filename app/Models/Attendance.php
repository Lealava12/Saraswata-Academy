<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['class_id', 'subject_id', 'attendance_date', 'slug', 'is_active'];
    protected $casts = ['attendance_date' => 'date'];

    public function classInfo()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function details()
    {
        return $this->hasMany(AttendanceDetail::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->slug = $m->slug ?: \Str::slug('att-' . uniqid()));
    }
}
