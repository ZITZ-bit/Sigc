<?php

namespace Database\Seeders;

use App\Models\Clinica;
use App\Models\Especialidad;
use Illuminate\Database\Seeder;

class ClinicaEspecialidadSeeder extends Seeder
{
    public function run(): void
    {
        Clinica::firstOrCreate(
            ['nombre' => 'Clínica SIGC Central'],
            ['direccion' => 'Av. Principal 123', 'telefono' => '+52 55 0000 0000', 'correo' => 'contacto@sigc.mx']
        );

        $especialidades = [
            'Cardiología',
            'Dermatología',
            'Pediatría',
            'Ginecología',
            'Traumatología',
            'Medicina general',
        ];

        foreach ($especialidades as $esp) {
            Especialidad::firstOrCreate(['nombre' => $esp]);
        }
    }
}
