<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        Material::firstOrCreate(['nombre' => 'Cemento'], ['categoria' => 'Construcción']);
        Material::firstOrCreate(['nombre' => 'Arena'], ['categoria' => 'Construcción']);
        Material::firstOrCreate(['nombre' => 'Grava'], ['categoria' => 'Construcción']);
        Material::firstOrCreate(['nombre' => 'Block'], ['categoria' => 'Construcción']);
        Material::firstOrCreate(['nombre' => 'Acero'], ['categoria' => 'Construcción']);
        Material::firstOrCreate(['nombre' => 'Madera'], ['categoria' => 'Carpintería']);
        Material::firstOrCreate(['nombre' => 'Pintura'], ['categoria' => 'Acabados']);
        Material::firstOrCreate(['nombre' => 'Tubería'], ['categoria' => 'Plomería']);
        Material::firstOrCreate(['nombre' => 'Cable eléctrico'], ['categoria' => 'Electricidad']);
    }
}