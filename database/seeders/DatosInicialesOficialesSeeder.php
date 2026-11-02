<?php

namespace Database\Seeders;

use App\Models\Consulta;
use App\Models\Departamento;
use App\Models\Especialidad;
use App\Models\HistorialClinico;
use App\Models\Horario;
use App\Models\Medico;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatosInicialesOficialesSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * Genera datos iniciales oficiales de demostración para SIGC.
     * Todo el seeder es idempotente: usa firstOrCreate y claves de búsqueda
     * determinísticas para evitar duplicados en lanzamientos repetidos.
     *
     * Correos:
     *   - Pacientes: personales ficticios @gmail.com
     *   - Médicos y coordinadores: institucionales @sigc.com
     *
     * Contraseñas: se generan con Hash::make() a partir de la variable
     * de entorno SEEDER_PASSWORD (valor por defecto temporal, cambiar en
     * producción). Nunca se almacena en texto plano.
     */
    public function run(): void
    {
        $this->ensureRoles();
        $this->crearEspecialidadesYDepartamentos();
        $this->crearPacientes();
        $this->crearMedicos();
        $this->crearCoordinadores();
        $this->asignarEspecialidadesDepartamentosAMedicos();
        $this->crearHorariosMedicos();
        $this->crearConsultasEHistoriales();
        $this->crearNotificaciones();
    }

    private function ensureRoles(): void
    {
        Role::firstOrCreate(['slug' => 'paciente'], ['name' => 'Paciente']);
        Role::firstOrCreate(['slug' => 'medico'], ['name' => 'Médico']);
        Role::firstOrCreate(['slug' => 'coordinador'], ['name' => 'Coordinador']);
    }

    private function crearEspecialidadesYDepartamentos(): void
    {
        $especialidades = [
            ['name' => 'Medicina General', 'slug' => 'medicina-general', 'description' => 'Atención primaria y familiar'],
            ['name' => 'Cardiología', 'slug' => 'cardiologia', 'description' => 'Enfermedades del corazón y sistema circulatorio'],
            ['name' => 'Pediatría', 'slug' => 'pediatria', 'description' => 'Salud de niños y adolescentes'],
            ['name' => 'Ginecología', 'slug' => 'ginecologia', 'description' => 'Salud reproductiva femenina'],
            ['name' => 'Traumatología', 'slug' => 'traumatologia', 'description' => 'Sistema musculoesquelético'],
            ['name' => 'Dermatología', 'slug' => 'dermatologia', 'description' => 'Enfermedades de la piel'],
            ['name' => 'Neurología', 'slug' => 'neurologia', 'description' => 'Sistema nervioso'],
            ['name' => 'Oftalmología', 'slug' => 'oftalmologia', 'description' => 'Salud visual y ocular'],
        ];

        foreach ($especialidades as $esp) {
            Especialidad::firstOrCreate(['slug' => $esp['slug']], $esp);
        }

        $departamentos = [
            ['name' => 'Consultas Externas', 'slug' => 'consultas-externas'],
            ['name' => 'Urgencias', 'slug' => 'urgencias'],
            ['name' => 'Hospitalización', 'slug' => 'hospitalizacion'],
            ['name' => 'Quirófano', 'slug' => 'quirófano'],
            ['name' => 'Imagenología', 'slug' => 'imagenologia'],
        ];

        foreach ($departamentos as $dep) {
            Departamento::firstOrCreate(['slug' => $dep['slug']], $dep);
        }
    }

    private function crearPacientes(): void
    {
        $rolPaciente = Role::where('slug', 'paciente')->firstOrFail();

        $pacientes = [
            ['name' => 'Gabriel Mendoza', 'email' => 'gabriel.mendoza@gmail.com'],
            ['name' => 'Lucía Fernández', 'email' => 'lucia.fernandez@gmail.com'],
            ['name' => 'Andrés Rojas', 'email' => 'andres.rojas@gmail.com'],
            ['name' => 'Valentina Suárez', 'email' => 'valentina.suarez@gmail.com'],
            ['name' => 'Mateo Castillo', 'email' => 'mateo.castillo@gmail.com'],
            ['name' => 'Camila Silva', 'email' => 'camila.silva@gmail.com'],
            ['name' => 'Sebastián Ramírez', 'email' => 'sebastian.ramirez@gmail.com'],
            ['name' => 'Mariana Torres', 'email' => 'mariana.torres@gmail.com'],
            ['name' => 'Julián Vargas', 'email' => 'julian.vargas@gmail.com'],
            ['name' => 'Sofía Herrera', 'email' => 'sofia.herrera@gmail.com'],
            ['name' => 'Diego Muñoz', 'email' => 'diego.munoz@gmail.com'],
            ['name' => 'Elena Duarte', 'email' => 'elena.duarte@gmail.com'],
            ['name' => 'Ricardo Fajardo', 'email' => 'ricardo.fajardo@gmail.com'],
            ['name' => 'Patricia León', 'email' => 'patricia.leon@gmail.com'],
            ['name' => 'Fernando Ibarra', 'email' => 'fernando.ibarra@gmail.com'],
            ['name' => 'Claudia Montes', 'email' => 'claudia.montes@gmail.com'],
            ['name' => 'Oscar Paredes', 'email' => 'oscar.paredes@gmail.com'],
            ['name' => 'Beatriz Salas', 'email' => 'beatriz.salas@gmail.com'],
            ['name' => 'Arturo Rivas', 'email' => 'arturo.rivas@gmail.com'],
            ['name' => 'Lorena Peña', 'email' => 'lorena.pena@gmail.com'],
        ];

        foreach ($pacientes as $paciente) {
            User::firstOrCreate(
                ['email' => $paciente['email']],
                [
                    'name' => $paciente['name'],
                    'password' => Hash::make(env('SEEDER_PASSWORD', 'ClaveInicial2026!')),
                    'role_id' => $rolPaciente->id,
                ]
            );
        }
    }

    private function crearMedicos(): void
    {
        $rolMedico = Role::where('slug', 'medico')->firstOrFail();

        $medicos = [
            ['name' => 'Dra. Elena García López', 'email' => 'elena.garcia@sigc.com', 'licencia' => 'MED-2024-001', 'telefono' => '+58 212 555 0101', 'fecha' => '2020-03-15'],
            ['name' => 'Dr. Carlos Ramírez Herrera', 'email' => 'carlos.ramirez@sigc.com', 'licencia' => 'MED-2024-002', 'telefono' => '+58 212 555 0102', 'fecha' => '2019-07-22'],
            ['name' => 'Dra. Isabela Díaz Méndez', 'email' => 'isabela.diaz@sigc.com', 'licencia' => 'MED-2024-003', 'telefono' => '+58 212 555 0103', 'fecha' => '2021-01-10'],
            ['name' => 'Dr. Roberto Sánchez', 'email' => 'roberto.sanchez@sigc.com', 'licencia' => 'MED-2024-004', 'telefono' => '+58 212 555 0104', 'fecha' => '2018-11-05'],
            ['name' => 'Dra. Patricia Flores', 'email' => 'patricia.flores@sigc.com', 'licencia' => 'MED-2024-005', 'telefono' => '+58 212 555 0105', 'fecha' => '2022-04-18'],
            ['name' => 'Dr. Jorge Castillo', 'email' => 'jorge.castillo@sigc.com', 'licencia' => 'MED-2024-006', 'telefono' => '+58 212 555 0106', 'fecha' => '2017-09-30'],
            ['name' => 'Dra. Marina Ortega', 'email' => 'marina.ortega@sigc.com', 'licencia' => 'MED-2024-007', 'telefono' => '+58 212 555 0107', 'fecha' => '2023-02-14'],
            ['name' => 'Dr. Luis Fernández', 'email' => 'luis.fernandez@sigc.com', 'licencia' => 'MED-2024-008', 'telefono' => '+58 212 555 0108', 'fecha' => '2016-06-08'],
            ['name' => 'Dra. Andrea Gómez', 'email' => 'andrea.gomez@sigc.com', 'licencia' => 'MED-2024-009', 'telefono' => '+58 212 555 0109', 'fecha' => '2021-12-03'],
            ['name' => 'Dr. Miguel Ángel Ruiz', 'email' => 'miguel.ruiz@sigc.com', 'licencia' => 'MED-2024-010', 'telefono' => '+58 212 555 0110', 'fecha' => '2015-08-17'],
            ['name' => 'Dra. Carolina Ibáñez', 'email' => 'carolina.ibanez@sigc.com', 'licencia' => 'MED-2024-011', 'telefono' => '+58 212 555 0111', 'fecha' => '2020-05-25'],
            ['name' => 'Dr. Víctor Silva', 'email' => 'victor.silva@sigc.com', 'licencia' => 'MED-2024-012', 'telefono' => '+58 212 555 0112', 'fecha' => '2019-02-28'],
            ['name' => 'Dra. Natalia Peralta', 'email' => 'natalia.peralta@sigc.com', 'licencia' => 'MED-2024-013', 'telefono' => '+58 212 555 0113', 'fecha' => '2022-10-12'],
            ['name' => 'Dr. Federico León', 'email' => 'federico.leon@sigc.com', 'licencia' => 'MED-2024-014', 'telefono' => '+58 212 555 0114', 'fecha' => '2018-03-19'],
            ['name' => 'Dra. Teresa Navarro', 'email' => 'teresa.navarro@sigc.com', 'licencia' => 'MED-2024-015', 'telefono' => '+58 212 555 0115', 'fecha' => '2021-07-07'],
            ['name' => 'Dr. Alejandro Mora', 'email' => 'alejandro.mora@sigc.com', 'licencia' => 'MED-2024-016', 'telefono' => '+58 212 555 0116', 'fecha' => '2017-12-11'],
            ['name' => 'Dra. Susana Cruz', 'email' => 'susana.cruz@sigc.com', 'licencia' => 'MED-2024-017', 'telefono' => '+58 212 555 0117', 'fecha' => '2023-05-04'],
            ['name' => 'Dr. Gustavo Herrera', 'email' => 'gustavo.herrera@sigc.com', 'licencia' => 'MED-2024-018', 'telefono' => '+58 212 555 0118', 'fecha' => '2016-04-23'],
            ['name' => 'Dra. Adriana Jiménez', 'email' => 'adriana.jimenez@sigc.com', 'licencia' => 'MED-2024-019', 'telefono' => '+58 212 555 0119', 'fecha' => '2020-09-01'],
            ['name' => 'Dr. Ernesto Pardo', 'email' => 'ernesto.pardo@sigc.com', 'licencia' => 'MED-2024-020', 'telefono' => '+58 212 555 0120', 'fecha' => '2015-11-14'],
        ];

        foreach ($medicos as $m) {
            $usuario = User::firstOrCreate(
                ['email' => $m['email']],
                [
                    'name' => $m['name'],
                    'password' => Hash::make(env('SEEDER_PASSWORD', 'ClaveInicial2026!')),
                    'role_id' => Role::where('slug', 'medico')->first()->id,
                ]
            );

            Medico::firstOrCreate(
                ['user_id' => $usuario->id],
                [
                    'numero_licencia' => $m['licencia'],
                    'telefono' => $m['telefono'],
                    'fecha_incorporacion' => $m['fecha'],
                    'estado' => 'activo',
                ]
            );
        }
    }

    private function crearCoordinadores(): void
    {
        $rolCoordinador = Role::where('slug', 'coordinador')->firstOrFail();

        $coordinadores = [
            ['name' => 'Lic. Andrea Sánchez', 'email' => 'andrea.sanchez@sigc.com'],
            ['name' => 'Lic. Roberto Méndez', 'email' => 'roberto.mendez@sigc.com'],
            ['name' => 'Lic. Claudia Rojas', 'email' => 'claudia.rojas@sigc.com'],
            ['name' => 'Lic. Diego Herrera', 'email' => 'diego.herrera@sigc.com'],
            ['name' => 'Lic. Fernanda Silva', 'email' => 'fernanda.silva@sigc.com'],
            ['name' => 'Lic. Miguel Torres', 'email' => 'miguel.torres@sigc.com'],
            ['name' => 'Lic. Paola Vargas', 'email' => 'paola.vargas@sigc.com'],
            ['name' => 'Lic. Luis Ramírez', 'email' => 'luis.ramirez@sigc.com'],
            ['name' => 'Lic. Gabriela Molina', 'email' => 'gabriela.molina@sigc.com'],
            ['name' => 'Lic. Javier Peña', 'email' => 'javier.pena@sigc.com'],
            ['name' => 'Lic. Mónica Díaz', 'email' => 'monica.diaz@sigc.com'],
            ['name' => 'Lic. Carlos Gutiérrez', 'email' => 'carlos.gutierrez@sigc.com'],
            ['name' => 'Lic. Elena Castro', 'email' => 'elena.castro@sigc.com'],
            ['name' => 'Lic. Marcos Domínguez', 'email' => 'marcos.dominguez@sigc.com'],
            ['name' => 'Lic. Sonia López', 'email' => 'sonia.lopez@sigc.com'],
            ['name' => 'Lic. Fernando Reyes', 'email' => 'fernando.reyes@sigc.com'],
            ['name' => 'Lic. Beatriz Méndez', 'email' => 'beatriz.mendez@sigc.com'],
            ['name' => 'Lic. Julio Salinas', 'email' => 'julio.salinas@sigc.com'],
            ['name' => 'Lic. Sandra Alcántara', 'email' => 'sandra.alcantara@sigc.com'],
            ['name' => 'Lic. Ricardo Mejía', 'email' => 'ricardo.mejia@sigc.com'],
        ];

        foreach ($coordinadores as $c) {
            User::firstOrCreate(
                ['email' => $c['email']],
                [
                    'name' => $c['name'],
                    'password' => Hash::make(env('SEEDER_PASSWORD', 'ClaveInicial2026!')),
                    'role_id' => $rolCoordinador->id,
                ]
            );
        }
    }

    private function asignarEspecialidadesDepartamentosAMedicos(): void
    {
        $especialidades = Especialidad::all();
        $departamentos = Departamento::all();

        if ($especialidades->isEmpty() || $departamentos->isEmpty()) {
            return;
        }

        $especialidadesArray = $especialidades->values()->all();
        $departamentosArray = $departamentos->values()->all();

        Medico::all()->each(function ($medico) use ($especialidadesArray, $departamentosArray) {
            $indiceE = $medico->id % count($especialidadesArray);
            $indiceD = $medico->id % count($departamentosArray);
            $medico->update([
                'especialidad_id' => $especialidadesArray[$indiceE]->id,
                'departamento_id' => $departamentosArray[$indiceD]->id,
            ]);
        });
    }

    private function crearHorariosMedicos(): void
    {
        $medicos = Medico::all();

        if ($medicos->isEmpty()) {
            return;
        }

        $dias = [1, 2, 3, 4, 5]; // Lunes a viernes

        $medicos->each(function ($medico) use ($dias) {
            foreach ($dias as $dia) {
                $horaInicio = sprintf('%02d:00:00', 8 + ($dia % 3));
                $horaFin = '17:00:00';

                Horario::firstOrCreate(
                    [
                        'medico_id' => $medico->id,
                        'dia_semana' => $dia,
                    ],
                    [
                        'hora_inicio' => $horaInicio,
                        'hora_fin' => $horaFin,
                        'activo' => true,
                    ]
                );
            }
        });
    }

    private function crearConsultasEHistoriales(): void
    {
        $pacientes = User::where('role_id', Role::where('slug', 'paciente')->first()->id)
            ->orderBy('id')
            ->take(15)
            ->get();

        $especialidades = Especialidad::all()->values()->all();
        $medicos = Medico::where('estado', 'activo')->get();

        if ($pacientes->isEmpty() || $especialidades === [] || $medicos->isEmpty()) {
            return;
        }

        $estados = ['pendiente', 'confirmada', 'completada', 'cancelada'];

        $motivos = [
            'Control médico general',
            'Dolor de cabeza recurrente',
            'Revisión postoperatoria',
            'Control prenatal',
            'Control de presión arterial',
            'Consulta por fiebre',
            'Control de diabetes',
            'Dolor articular',
            'Consulta de alergias',
            'Control de colesterol',
            'Revisión de visión',
            'Consulta dermatológica',
            'Control de embarazo',
            'Dolor de espalda',
            'Consulta cardiológica',
        ];

        foreach ($pacientes as $indicePaciente => $paciente) {
            $numConsultas = ($indicePaciente % 3) + 1;

            for ($j = 0; $j < $numConsultas; $j++) {
                $fecha = Carbon::create(2026, 5, 1)
                    ->addDays($indicePaciente)
                    ->addWeeks($j)
                    ->format('Y-m-d');

                $hora = sprintf('%02d:%02d:00', 9 + ($j % 4), ($j * 15) % 60);

                $indiceEspecialidad = ($indicePaciente + $j) % count($especialidades);
                $especialidad = $especialidades[$indiceEspecialidad];

                $medico = $medicos->where('especialidad_id', $especialidad->id)->first()
                    ?? $medicos->first();

                $motivo = $motivos[($indicePaciente + $j) % count($motivos)];
                $estado = $estados[($indicePaciente + $j) % count($estados)];

                $consulta = Consulta::firstOrCreate(
                    [
                        'user_id' => $paciente->id,
                        'fecha' => $fecha,
                        'hora' => $hora,
                        'motivo' => $motivo,
                    ],
                    [
                        'medico_id' => $medico->id,
                        'especialidad_id' => $especialidad->id,
                        'estado' => $estado,
                    ]
                );

                if (in_array($estado, ['completada', 'confirmada'])) {
                    HistorialClinico::firstOrCreate(
                        ['consulta_id' => $consulta->id],
                        [
                            'paciente_id' => $paciente->id,
                            'medico_id' => $medico->id,
                            'nota' => 'Nota clínica de seguimiento para consulta '.($j + 1),
                            'diagnostico' => 'Diagnóstico de seguimiento '.($j + 1),
                            'tratamiento' => 'Tratamiento de seguimiento '.($j + 1),
                        ]
                    );
                }
            }
        }
    }

    private function crearNotificaciones(): void
    {
        $pacientes = User::where('role_id', Role::where('slug', 'paciente')->first()->id)
            ->orderBy('id')
            ->take(10)
            ->get();

        $medicos = Medico::all();

        if ($pacientes->isEmpty()) {
            return;
        }

        $tipos = [
            'App\Notifications\ConsultaSolicitada',
            'App\Notifications\ConsultaConfirmada',
            'App\Notifications\ConsultaCompletada',
        ];

        $mensajes = [
            'Su solicitud de consulta ha sido recibida.',
            'Su consulta ha sido confirmada por el médico.',
            'Su consulta ha sido completada.',
            'Tiene una nueva cita programada.',
            'Recordatorio de consulta médica.',
        ];

        foreach ($pacientes as $indice => $paciente) {
            $tipo = $tipos[$indice % count($tipos)];
            $mensaje = $mensajes[$indice % count($mensajes)];

            $existe = DB::table('notifications')
                ->where('notifiable_id', $paciente->id)
                ->where('type', $tipo)
                ->exists();

            if (! $existe) {
                DB::table('notifications')->insert([
                    'id' => Str::uuid(),
                    'type' => $tipo,
                    'notifiable_type' => User::class,
                    'notifiable_id' => $paciente->id,
                    'data' => json_encode(['message' => $mensaje]),
                    'read_at' => $indice % 3 === 0 ? now() : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if ($medicos->isNotEmpty()) {
            foreach ($medicos->take(5) as $indice => $medico) {
                if ($medico->user) {
                    $tipo = 'App\Notifications\ConsultaAsignada';

                    $existe = DB::table('notifications')
                        ->where('notifiable_id', $medico->user->id)
                        ->where('type', $tipo)
                        ->exists();

                    if (! $existe) {
                        DB::table('notifications')->insert([
                            'id' => Str::uuid(),
                            'type' => $tipo,
                            'notifiable_type' => User::class,
                            'notifiable_id' => $medico->user->id,
                            'data' => json_encode(['message' => 'Tiene una nueva consulta asignada.']),
                            'read_at' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
