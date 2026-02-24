<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyMaterial extends Model
{
    protected $table = 'study_materials';
    protected $fillable = ['name', 'description', 'slug', 'is_active'];

    public function studentMaterials()
    {
        return $this->hasMany(StudentMaterial::class, 'material_id');
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
