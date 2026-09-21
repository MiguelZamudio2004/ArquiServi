<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profesion extends Model
{
    protected $table = 'profesiones';
    protected $fillable = ['nombre', 'descripcion', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function especialidades()
    {
        return $this->hasMany(Especialidad::class, 'profesion_id');
    }

    public function profesionales()
    {
        return $this->belongsToMany(Profesional::class, 'profesional_profesion')->withTimestamps();
    }
}