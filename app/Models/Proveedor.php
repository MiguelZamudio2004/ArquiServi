<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'usuario_id',
        'descripcion',
        'zona_trabajo',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function materiales()
    {
        return $this->belongsToMany(Material::class, 'proveedor_material');
    }
}