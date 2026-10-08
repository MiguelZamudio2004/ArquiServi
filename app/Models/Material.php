<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Material extends Model
{
    use HasFactory;

    protected $table = 'materiales';

    protected $fillable = [
        'nombre',
        'categoria',
        'descripcion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function proveedores(): BelongsToMany
    {
        return $this->belongsToMany(
            Proveedor::class,
            'proveedor_material',
            'material_id',
            'proveedor_id'
        )
            ->withPivot('disponible')
            ->withTimestamps();
    }

    public function solicitudes(): BelongsToMany
    {
        return $this->belongsToMany(
            Solicitud::class,
            'material_solicitud',
            'material_id',
            'solicitud_id'
        )->withTimestamps();
    }
}