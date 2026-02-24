<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $fillable = ['name', 'slug', 'is_active'];

    public function classes()
    {
        return $this->hasMany(Classes::class);
    }
    public function students()
    {
        return $this->hasMany(Student::class);
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
