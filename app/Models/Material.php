<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materiales';

    protected $fillable = [
        'nombre',
        'categoria',
        'descripcion',
        'activo',
    ];

    public function proveedores()
    {
        return $this->belongsToMany(Proveedor::class, 'proveedor_material');
    }
}