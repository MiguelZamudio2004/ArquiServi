<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materiales = [
            [
                'nombre' => 'Cemento',
                'categoria' => 'Construcción',
                'descripcion' => 'Material utilizado para la elaboración de concreto, mortero y trabajos generales de construcción.',
            ],
            [
                'nombre' => 'Arena',
                'categoria' => 'Construcción',
                'descripcion' => 'Agregado fino utilizado en mezclas de concreto, mortero y trabajos de albañilería.',
            ],
            [
                'nombre' => 'Grava',
                'categoria' => 'Construcción',
                'descripcion' => 'Agregado utilizado principalmente para la elaboración de concreto.',
            ],
            [
                'nombre' => 'Block',
                'categoria' => 'Construcción',
                'descripcion' => 'Elemento prefabricado utilizado para la construcción de muros y divisiones.',
            ],
            [
                'nombre' => 'Ladrillo',
                'categoria' => 'Construcción',
                'descripcion' => 'Elemento utilizado en la construcción de muros, divisiones y elementos arquitectónicos.',
            ],
            [
                'nombre' => 'Tabique',
                'categoria' => 'Construcción',
                'descripcion' => 'Material utilizado para la construcción de muros y divisiones.',
            ],
            [
                'nombre' => 'Varilla',
                'categoria' => 'Estructura',
                'descripcion' => 'Acero de refuerzo utilizado en columnas, castillos, losas y otros elementos estructurales.',
            ],
            [
                'nombre' => 'Acero estructural',
                'categoria' => 'Estructura',
                'descripcion' => 'Material utilizado en estructuras metálicas y elementos de soporte.',
            ],
            [
                'nombre' => 'Malla electrosoldada',
                'categoria' => 'Estructura',
                'descripcion' => 'Malla de acero utilizada como refuerzo en pisos, losas y elementos de concreto.',
            ],
            [
                'nombre' => 'Alambre recocido',
                'categoria' => 'Estructura',
                'descripcion' => 'Alambre utilizado para amarre de varilla y trabajos de construcción.',
            ],
            [
                'nombre' => 'Madera',
                'categoria' => 'Carpintería',
                'descripcion' => 'Material utilizado en carpintería, estructuras, muebles y acabados.',
            ],
            [
                'nombre' => 'Triplay',
                'categoria' => 'Carpintería',
                'descripcion' => 'Panel de madera utilizado en muebles, recubrimientos y trabajos de carpintería.',
            ],
            [
                'nombre' => 'MDF',
                'categoria' => 'Carpintería',
                'descripcion' => 'Tablero de fibras de madera utilizado principalmente en muebles y acabados interiores.',
            ],
            [
                'nombre' => 'Pintura',
                'categoria' => 'Acabados',
                'descripcion' => 'Recubrimiento utilizado para proteger y decorar superficies interiores y exteriores.',
            ],
            [
                'nombre' => 'Yeso',
                'categoria' => 'Acabados',
                'descripcion' => 'Material utilizado para recubrimientos, reparaciones y acabados interiores.',
            ],
            [
                'nombre' => 'Azulejo',
                'categoria' => 'Acabados',
                'descripcion' => 'Recubrimiento cerámico utilizado principalmente en baños, cocinas y muros.',
            ],
            [
                'nombre' => 'Loseta',
                'categoria' => 'Acabados',
                'descripcion' => 'Recubrimiento utilizado para pisos y diferentes superficies.',
            ],
            [
                'nombre' => 'Piso cerámico',
                'categoria' => 'Acabados',
                'descripcion' => 'Recubrimiento cerámico utilizado en pisos interiores y exteriores.',
            ],
            [
                'nombre' => 'Adhesivo para piso',
                'categoria' => 'Acabados',
                'descripcion' => 'Adhesivo utilizado para la instalación de pisos, losetas y recubrimientos cerámicos.',
            ],
            [
                'nombre' => 'Boquilla',
                'categoria' => 'Acabados',
                'descripcion' => 'Material utilizado para rellenar juntas entre piezas de piso y azulejo.',
            ],
            [
                'nombre' => 'Tubería PVC',
                'categoria' => 'Plomería',
                'descripcion' => 'Tubería utilizada principalmente en instalaciones sanitarias y drenaje.',
            ],
            [
                'nombre' => 'Tubería CPVC',
                'categoria' => 'Plomería',
                'descripcion' => 'Tubería utilizada en instalaciones hidráulicas para agua fría y caliente.',
            ],
            [
                'nombre' => 'Tubería PPR',
                'categoria' => 'Plomería',
                'descripcion' => 'Tubería utilizada en sistemas hidráulicos de agua fría y caliente.',
            ],
            [
                'nombre' => 'Conexiones hidráulicas',
                'categoria' => 'Plomería',
                'descripcion' => 'Accesorios utilizados para unir y distribuir tuberías en instalaciones hidráulicas.',
            ],
            [
                'nombre' => 'Cable eléctrico',
                'categoria' => 'Electricidad',
                'descripcion' => 'Cable conductor utilizado en instalaciones eléctricas residenciales y comerciales.',
            ],
            [
                'nombre' => 'Tubo conduit',
                'categoria' => 'Electricidad',
                'descripcion' => 'Tubería utilizada para proteger y conducir cableado eléctrico.',
            ],
            [
                'nombre' => 'Centro de carga',
                'categoria' => 'Electricidad',
                'descripcion' => 'Gabinete utilizado para alojar interruptores y distribuir circuitos eléctricos.',
            ],
            [
                'nombre' => 'Interruptores',
                'categoria' => 'Electricidad',
                'descripcion' => 'Dispositivos utilizados para controlar circuitos y equipos eléctricos.',
            ],
            [
                'nombre' => 'Contactos eléctricos',
                'categoria' => 'Electricidad',
                'descripcion' => 'Dispositivos utilizados para conectar equipos eléctricos a la instalación.',
            ],
            [
                'nombre' => 'Impermeabilizante',
                'categoria' => 'Impermeabilización',
                'descripcion' => 'Recubrimiento utilizado para prevenir filtraciones de agua y humedad.',
            ],
            [
                'nombre' => 'Membrana impermeabilizante',
                'categoria' => 'Impermeabilización',
                'descripcion' => 'Material utilizado para proteger superficies contra filtraciones y humedad.',
            ],
            [
                'nombre' => 'Sellador',
                'categoria' => 'Impermeabilización',
                'descripcion' => 'Producto utilizado para sellar superficies, juntas y pequeñas filtraciones.',
            ],
            [
                'nombre' => 'Perfil metálico',
                'categoria' => 'Herrería',
                'descripcion' => 'Elemento metálico utilizado en estructuras, puertas, ventanas y trabajos de herrería.',
            ],
            [
                'nombre' => 'Lámina',
                'categoria' => 'Herrería',
                'descripcion' => 'Material metálico utilizado en cubiertas, estructuras y trabajos de fabricación.',
            ],
            [
                'nombre' => 'Vidrio',
                'categoria' => 'Vidriería',
                'descripcion' => 'Material utilizado en ventanas, puertas, canceles y elementos arquitectónicos.',
            ],
        ];

        foreach ($materiales as $datosMaterial) {
            Material::updateOrCreate(
                [
                    'nombre' => $datosMaterial['nombre'],
                ],
                [
                    'categoria' => $datosMaterial['categoria'],
                    'descripcion' => $datosMaterial['descripcion'],
                    'activo' => true,
                ]
            );
        }
    }
}