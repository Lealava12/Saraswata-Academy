<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['class_id', 'attendance_date', 'slug', 'is_active'];
    
    protected $casts = [
        'attendance_date' => 'date',
    ];
    
    protected $table = 'attendances';

    public function classInfo()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function details()
    {
        return $this->hasMany(AttendanceDetail::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'attendance_subject')
            ->withTimestamps();
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->slug = $m->slug ?: \Str::slug('att-' . uniqid()));
    }
}