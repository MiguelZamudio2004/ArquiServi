<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

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
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
        ];
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function proveedor()
    {
        return $this->hasOne(Proveedor::class, 'usuario_id');
    }

    public function profesional()
    {
        return $this->hasOne(Profesional::class, 'usuario_id');
    }

    public function codigosRecuperacion()
    {
        return $this->hasMany(CodigoRecuperacion::class, 'usuario_id');
    }

    public function solicitudes() {
        return $this->hasMany(Solicitud::class,'usuario_id');
    }

    public function telefonoWhatsapp()
{
    if (!$this->telefono) return null;

    $telefono = preg_replace('/\D/', '', $this->telefono);

    if (strlen($telefono) === 10) {
        $telefono = '52' . $telefono;
    }

    return $telefono;
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

}