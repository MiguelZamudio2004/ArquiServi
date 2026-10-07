<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdministradorSeeder extends Seeder
{
    public function run(): void
    {
        $correo = config('admin.email');
        $password = config('admin.password');

        $rolAdministrador = Rol::firstOrCreate([
            'nombre' => 'administrador'
        ]);

        $usuarioExistente = Usuario::where(
            'correo',
            $correo
        )->first();

        if (
            $usuarioExistente &&
            $usuarioExistente->rol_id != $rolAdministrador->id
        ) {
            throw new RuntimeException(
                'El correo del administrador ya pertenece a otro usuario.'
            );
        }

        Usuario::updateOrCreate(
            [
                'correo' => $correo
            ],
            [
                'rol_id' => $rolAdministrador->id,
                'nombre' => config('admin.nombre'),
                'apellido_paterno' => config(
                    'admin.apellido_paterno'
                ),
                'apellido_materno' => config(
                    'admin.apellido_materno'
                ),
                'telefono' => null,
                'password' => Hash::make(
                    $password
                ),
                'estado' => 'activo'
            ]
        );
    }
}