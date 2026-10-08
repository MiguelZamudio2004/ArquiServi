<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Especialidad extends Model
{
    protected $table = 'especialidades';

    protected $fillable = [
        'profesion_id',
        'nombre',
        'descripcion',
        'activo',
        'requiere_aprobacion',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'requiere_aprobacion' => 'boolean',
        ];
    }

    public function profesion(): BelongsTo
    {
        return $this->belongsTo(Profesion::class, 'profesion_id');
    }

    public function profesionales(): BelongsToMany
    {
        return $this->belongsToMany(
            Profesional::class,
            'profesional_especialidad',
            'especialidad_id',
            'profesional_id'
        )->withTimestamps();
    }
}