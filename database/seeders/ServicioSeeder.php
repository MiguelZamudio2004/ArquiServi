<?php

namespace Database\Seeders;

use App\Models\Servicio;
use Illuminate\Database\Seeder;

class ServicioSeeder extends Seeder
{
    public function run(): void
    {
        Servicio::firstOrCreate(['nombre' =>'Diseño de planos']);
        Servicio::firstOrCreate(['nombre' =>'Diseño arquitectonico']);
        Servicio::firstOrCreate(['nombre' =>'Supervision de obra']);
        Servicio::firstOrCreate(['nombre' =>'Remodelacion']);
        Servicio::firstOrCreate(['nombre' =>'Instalacion electrica']);
        Servicio::firstOrCreate(['nombre' =>'Instalacion hidraulica']);
    }
}
