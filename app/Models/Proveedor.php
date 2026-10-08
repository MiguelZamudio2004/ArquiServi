<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'usuario_id',
        'descripcion',
        'zona_trabajo',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function materiales(): BelongsToMany
    {
        return $this->belongsToMany(
            Material::class,
            'proveedor_material',
            'proveedor_id',
            'material_id'
        )
            ->withPivot('disponible')
            ->withTimestamps();
    }

    public function solicitudesRecibidas(): HasMany
    {
        return $this->hasMany(
            Solicitud::class,
            'destinatario_id',
            'usuario_id'
        );
    }
}