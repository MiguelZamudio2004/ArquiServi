<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodigoRecuperacion extends Model
{
    protected $table = 'codigo_recuperacion';

    protected $fillable = [
        'usuario_id',
        'codigo',
        'expira_en',
        'usado_en',
    ];

    protected function casts(): array
    {
        return [
            'expira_en' => 'datetime',
            'usado_en' => 'datetime',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
