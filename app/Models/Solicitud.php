<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $table = 'solicitudes';

    protected $fillable = [
        'usuario_id',
        'profesional_id',
        'servicio_id',
        'descripcion',
        'estado'
    ];

    public function usuario()
    {
        return $this->belongsTo(
            Usuario::class,
            'usuario_id'
        );
    }

    public function profesional()
    {
        return $this->belongsTo(
            Profesional::class,
            'profesional_id'
        );
    }

    public function servicio()
    {
        return $this->belongsTo(
            Servicio::class,
            'servicio_id'
        );
    }

    public function calificaciones()
    {
        return $this->hasMany(
            Calificacion::class,
            'solicitud_id'
        );
    }
}