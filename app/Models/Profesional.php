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
        'zona_trabajo'
    ];

    public function usuario()
    {
        return $this->belongsTo(
            Usuario::class,
            'usuario_id'
        );
    }

    public function profesiones()
    {
        return $this->belongsToMany(
            Profesion::class,
            'profesional_profesion',
            'profesional_id',
            'profesion_id'
        );
    }

    public function especialidades()
    {
        return $this->belongsToMany(
            Especialidad::class,
            'profesional_especialidad',
            'profesional_id',
            'especialidad_id'
        );
    }

    public function servicios()
    {
        return $this->belongsToMany(
            Servicio::class,
            'profesional_servicio',
            'profesional_id',
            'servicio_id'
        )->withTimestamps();
    }

    public function solicitudesRecibidas()
    {
        return $this->hasMany(
            Solicitud::class,
            'destinatario_id',
            'usuario_id'
        );
    }
}