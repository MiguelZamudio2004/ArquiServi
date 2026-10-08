<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servicio extends Model
{
    protected $table = 'servicios';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function profesionales(): BelongsToMany
    {
        return $this->belongsToMany(
            Profesional::class,
            'profesional_servicio'
        )->withTimestamps();
    }

    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'servicio_id');
    }
}