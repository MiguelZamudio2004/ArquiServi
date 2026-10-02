<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function proveedores()
    {
        return $this->belongsToMany(
            Proveedor::class,
            'material_proveedor',
            'material_id',
            'proveedor_id'
        )->withPivot('disponible')
        ->withTimestamps();
    }

    public function solicitudes()
    {
        return $this->belongsToMany(
            Solicitud::class,
            'material_solicitud',
            'material_id',
            'solicitud_id'
        )->withTimestamps();
    }
}