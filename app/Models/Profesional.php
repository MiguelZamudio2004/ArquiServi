<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Profesional extends Model
{
    private const ESTADO_APROBADO = 'aprobado';

    protected $table = 'profesionales';

    protected $fillable = [
        'usuario_id',
        'anios_experiencia',
        'descripcion',
        'portafolio_url',
        'zona_trabajo',
        'estado_aprobacion',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function profesiones(): BelongsToMany
    {
        return $this->belongsToMany(
            Profesion::class,
            'profesional_profesion',
            'profesional_id',
            'profesion_id'
        )->withTimestamps();
    }

    public function especialidades(): BelongsToMany
    {
        return $this->belongsToMany(
            Especialidad::class,
            'profesional_especialidad',
            'profesional_id',
            'especialidad_id'
        )->withTimestamps();
    }

    public function servicios(): BelongsToMany
    {
        return $this->belongsToMany(
            Servicio::class,
            'profesional_servicio',
            'profesional_id',
            'servicio_id'
        )->withTimestamps();
    }

    public function solicitudesRecibidas(): HasMany
    {
        return $this->hasMany(
            Solicitud::class,
            'destinatario_id',
            'usuario_id'
        );
    }

    public function solicitudesAprobacion(): HasMany
    {
        return $this->hasMany(
            SolicitudAprobacionProfesional::class,
            'profesional_id'
        );
    }

    public function estaDisponiblePublicamente(): bool
    {
        return $this->usuario?->estado === 'activo';
    }

    public function especialidadesVisibles(): Collection
    {
        return $this->especialidades
            ->filter(function (Especialidad $especialidad) {
                return !$especialidad->requiere_aprobacion
                    || $this->estado_aprobacion === self::ESTADO_APROBADO;
            })
            ->values();
    }
}