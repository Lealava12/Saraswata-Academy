<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = ['class_id', 'subject_id', 'exam_date', 'full_marks', 'slug', 'is_active'];
    protected $casts = ['exam_date' => 'date'];

    public function classInfo()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function marks()
    {
        return $this->hasMany(ExamMark::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->slug = $m->slug ?: \Str::slug('exam-' . uniqid()));
    }
}
