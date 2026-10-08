<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'solicitante_id',
        'destinatario_id',
        'servicio_id',
        'descripcion',
        'estado',
    ];

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'solicitante_id');
    }

    public function destinatario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'destinatario_id');
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    public function materiales(): BelongsToMany
    {
        return $this->belongsToMany(
            Material::class,
            'material_solicitud',
            'solicitud_id',
            'material_id'
        )->withTimestamps();
    }

    public function calificaciones(): HasMany
    {
        return $this->hasMany(Calificacion::class, 'solicitud_id');
    }

    public function getMaterialAttribute(): ?Material
    {
        return $this->materiales->first();
    }
}