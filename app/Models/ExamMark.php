<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamMark extends Model
{
    protected $table = 'exam_marks';
    protected $fillable = ['exam_id', 'student_id', 'marks_obtained', 'slug', 'is_active'];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->slug = $m->slug ?: \Str::slug('em-' . uniqid()));
    }
}
