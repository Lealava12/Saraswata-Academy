<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceDetail extends Model
{
    protected $table = 'attendance_details';
    protected $fillable = ['attendance_id', 'student_id', 'subject_id', 'status', 'slug', 'is_active'];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->slug = $m->slug ?: \Str::slug('ad-' . uniqid()));
    }
}
