<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    protected $table = 'calificaciones';

    protected $fillable = [
        'solicitud_id',
        'evaluador_id',
        'evaluado_id',
        'tipo_evaluado',
        'criterios',
        'promedio',
        'comentario'
    ];

    protected $casts = [
        'criterios' => 'array',
        'promedio' => 'decimal:1'
    ];

    public function solicitud()
    {
        return $this->belongsTo(
            Solicitud::class,
            'solicitud_id'
        );
    }

    public function evaluador()
    {
        return $this->belongsTo(
            Usuario::class,
            'evaluador_id'
        );
    }

    public function evaluado()
    {
        return $this->belongsTo(
            Usuario::class,
            'evaluado_id'
        );
    }
}