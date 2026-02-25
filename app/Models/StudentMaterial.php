<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentMaterial extends Model
{
    protected $table = 'student_materials';
    protected $fillable = [
        'student_id', 
        'study_material_id',   
        'issue_date', 
        'status', 
        'slug', 
        'is_active'
    ];
    
    protected $casts = [
        'issue_date' => 'date',
       
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    
    public function material()
    {
        // Use the correct foreign key
        return $this->belongsTo(StudyMaterial::class, 'study_material_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->slug = $m->slug ?: \Str::slug('sm-' . uniqid()));
    }
    
}