<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    private const PREFIJO_MEXICO = '52';
    private const PREFIJO_MEXICO_ANTIGUO = '521';

    protected $table = 'usuarios';

    protected $fillable = [
        'rol_id',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'correo',
        'telefono',
        'password',
        'ubicacion',
        'descripcion',
        'foto_perfil',
        'portafolio_fotos',
        'estado',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'portafolio_fotos' => 'array',
        ];
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function profesional(): HasOne
    {
        return $this->hasOne(Profesional::class, 'usuario_id');
    }

    public function proveedor(): HasOne
    {
        return $this->hasOne(Proveedor::class, 'usuario_id');
    }

    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'solicitante_id');
    }

    public function solicitudesRealizadas(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'solicitante_id');
    }

    public function solicitudesRecibidas(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'destinatario_id');
    }

    public function calificacionesRealizadas(): HasMany
    {
        return $this->hasMany(Calificacion::class, 'evaluador_id');
    }

    public function calificacionesRecibidas(): HasMany
    {
        return $this->hasMany(Calificacion::class, 'evaluado_id');
    }

    public function telefonoWhatsapp(): ?string
    {
        if (!$this->telefono) {
            return null;
        }

        $telefono = preg_replace('/\D/', '', $this->telefono) ?? '';

        if (strlen($telefono) === 10) {
            return self::PREFIJO_MEXICO . $telefono;
        }

        if (
            strlen($telefono) === 13 &&
            str_starts_with($telefono, self::PREFIJO_MEXICO_ANTIGUO)
        ) {
            return self::PREFIJO_MEXICO . substr($telefono, 3);
        }

        return $telefono;
    }
}