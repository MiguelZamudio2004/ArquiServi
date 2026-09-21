<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    protected $table = 'especialidades';
    protected $fillable = ['profesion_id', 'nombre', 'descripcion', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function profesion()
    {
        return $this->belongsTo(Profesion::class, 'profesion_id');
    }

    public function profesionales()
    {
        return $this->belongsToMany(Profesional::class, 'profesional_especialidad')->withTimestamps();
    }
}