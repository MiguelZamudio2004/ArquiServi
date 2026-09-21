<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profesional extends Model
{
    protected $table = 'profesionales';

    protected $fillable = [
        'usuario_id',
        'anios_experiencia',
        'descripcion',
        'portafolio_url',
        'zona_trabajo',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function profesiones()
    {
        return $this->belongsToMany(Profesion::class, 'profesional_profesion')->withTimestamps();
    }

    public function especialidades()
    {
        return $this->belongsToMany(Especialidad::class, 'profesional_especialidad')->withTimestamps();
    }
}