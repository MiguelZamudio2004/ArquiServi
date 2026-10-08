<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profesion extends Model
{
    protected $table = 'profesiones';

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

    public function especialidades(): HasMany
    {
        return $this->hasMany(Especialidad::class, 'profesion_id');
    }

    public function profesionales(): BelongsToMany
    {
        return $this->belongsToMany(
            Profesional::class,
            'profesional_profesion',
            'profesion_id',
            'profesional_id'
        )->withTimestamps();
    }
}