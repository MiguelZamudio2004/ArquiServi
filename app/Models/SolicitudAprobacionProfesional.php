<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudAprobacionProfesional extends Model
{
    protected $table = 'solicitudes_aprobacion_profesional';

    protected $fillable = [
        'profesional_id',
        'especialidades_requieren_aprobacion',
        'estado',
        'revisado_por',
        'motivo_rechazo',
        'revisado_at'
    ];

    protected function casts(): array
    {
        return [
            'especialidades_requieren_aprobacion' => 'array',
            'revisado_at' => 'datetime'
        ];
    }

    public function profesional()
    {
        return $this->belongsTo(
            Profesional::class,
            'profesional_id'
        );
    }

    public function revisor()
    {
        return $this->belongsTo(
            Usuario::class,
            'revisado_por'
        );
    }
}