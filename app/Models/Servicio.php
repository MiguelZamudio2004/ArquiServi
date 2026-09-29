<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = 'servicios';
    protected $fillable = ['nombre', 'descripcion', 'activo'];

    protected function casts(): array {
        return ['activo' => 'boolean'];
    }

    public function profesionales() {
        return $this->belongsToMany(Profesional::class, 'profesional_servicio')->withTimestamps();
    }

    public function solicitudes() {
        return $this->hasMany(Solicitud::class, 'servicio_id');
    }
}
