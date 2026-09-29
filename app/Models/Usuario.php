<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

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
        'estado',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    public function rol()
    {
        return $this->belongsTo(
            Rol::class,
            'rol_id'
        );
    }

    public function profesional()
    {
        return $this->hasOne(
            Profesional::class,
            'usuario_id'
        );
    }

    public function proveedor()
    {
        return $this->hasOne(
            Proveedor::class,
            'usuario_id'
        );
    }

    public function solicitudes()
    {
        return $this->hasMany(
            Solicitud::class,
            'solicitante_id'
        );
    }

    public function solicitudesRealizadas()
    {
        return $this->hasMany(
            Solicitud::class,
            'solicitante_id'
        );
    }

    public function solicitudesRecibidas()
    {
        return $this->hasMany(
            Solicitud::class,
            'destinatario_id'
        );
    }

    public function calificacionesRealizadas()
    {
        return $this->hasMany(
            Calificacion::class,
            'evaluador_id'
        );
    }

    public function calificacionesRecibidas()
    {
        return $this->hasMany(
            Calificacion::class,
            'evaluado_id'
        );
    }

    public function telefonoWhatsapp()
    {
        if (!$this->telefono) {
            return null;
        }

        $telefono = preg_replace(
            '/\D/',
            '',
            $this->telefono
        );

        if (strlen($telefono) === 10) {
            return '52' . $telefono;
        }

        if (
            strlen($telefono) === 13 &&
            str_starts_with($telefono, '521')
        ) {
            return '52' . substr(
                $telefono,
                3
            );
        }

        return $telefono;
    }
}