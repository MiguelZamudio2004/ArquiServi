<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $table = 'solicitudes';

    protected $fillable = [
        'solicitante_id',
        'destinatario_id',
        'servicio_id',
        'material_id',
        'descripcion',
        'estado'
    ];

    public function solicitante()
    {
        return $this->belongsTo(
            Usuario::class,
            'solicitante_id'
        );
    }

    public function destinatario()
    {
        return $this->belongsTo(
            Usuario::class,
            'destinatario_id'
        );
    }

    public function servicio()
    {
        return $this->belongsTo(
            Servicio::class,
            'servicio_id'
        );
    }

    public function material()
    {
        return $this->belongsTo(
            Material::class,
            'material_id'
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