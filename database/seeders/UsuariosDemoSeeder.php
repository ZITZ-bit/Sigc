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

class UsuariosDemoSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $this->ensureRoles();
        $this->crearPacientes();
        $this->crearMedicos();
        $this->crearCoordinadores();
        $this->crearEspecialidadesYDepartamentos();
        $this->asignarEspecialidadesDepartamentosAMedicos();
        $this->crearConsultasEHistoriales();
        $this->crearHorariosMedicos();
        $this->crearNotificaciones();
    }

    private function ensureRoles(): void
    {
        Role::firstOrCreate(['slug' => 'paciente'], ['name' => 'Paciente']);
        Role::firstOrCreate(['slug' => 'medico'], ['name' => 'Médico']);
        Role::firstOrCreate(['slug' => 'coordinador'], ['name' => 'Coordinador']);
    }

    private function crearPacientes(): void
    {
        $rolPaciente = Role::where('slug', 'paciente')->firstOrFail();

        for ($i = 1; $i <= 20; $i++) {
            $email = "paciente.demo{$i}@sigc.test";

            User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => "Paciente Demo {$i}",
                    'password' => Hash::make('Password123!'),
                    'role_id' => $rolPaciente->id,
                ]
            );
        }
    }

    private function crearMedicos(): void
    {
        $rolMedico = Role::where('slug', 'medico')->firstOrFail();

        for ($i = 1; $i <= 20; $i++) {
            $email = "medico.demo{$i}@sigc.test";

            $usuario = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => "Médico Demo {$i}",
                    'password' => Hash::make('Password123!'),
                    'role_id' => $rolMedico->id,
                ]
            );

            Medico::firstOrCreate(
                ['user_id' => $usuario->id],
                [
                    'numero_licencia' => "LIC-{$i}-".Str::random(6),
                    'telefono' => fake()->phoneNumber(),
                    'fecha_incorporacion' => Carbon::now()->subDays(random_int(30, 730))->format('Y-m-d'),
                    'estado' => 'activo',
                ]
            );
        }
    }

    private function crearCoordinadores(): void
    {
        $rolCoordinador = Role::where('slug', 'coordinador')->firstOrFail();

        for ($i = 1; $i <= 20; $i++) {
            $email = "coordinador.demo{$i}@sigc.test";

            User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => "Coordinador Demo {$i}",
                    'password' => Hash::make('Password123!'),
                    'role_id' => $rolCoordinador->id,
                ]
            );
        }
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

    private function asignarEspecialidadesDepartamentosAMedicos(): void
    {
        $especialidades = Especialidad::all();
        $departamentos = Departamento::all();

        if ($especialidades->isEmpty() || $departamentos->isEmpty()) {
            return;
        }

        Medico::all()->each(function ($medico) use ($especialidades, $departamentos) {
            $medico->update([
                'especialidad_id' => $especialidades->random()->id,
                'departamento_id' => $departamentos->random()->id,
            ]);
        });
    }

    private function crearConsultasEHistoriales(): void
    {
        $pacientes = User::where('role_id', Role::where('slug', 'paciente')->first()->id)
            ->take(15)
            ->get();

        $especialidades = Especialidad::all();
        $medicos = Medico::where('estado', 'activo')->get();

        if ($pacientes->isEmpty() || $especialidades->isEmpty() || $medicos->isEmpty()) {
            return;
        }

        $estados = ['pendiente', 'confirmada', 'completada', 'cancelada'];

        $motivos = [
            'Consulta de control general',
            'Dolor de cabeza recurrente',
            'Revisión postoperatoria',
            'Chequeo prenatal',
            'Control de presión arterial',
            'Consulta por fiebre',
            'Revisión de diabetes',
            'Dolor articular',
            'Consulta de alergias',
            'Control de colesterol',
            'Revisión de visión',
            'Consulta dermatológica',
            'Control de embarazo',
            'Dolor de espalda',
            'Consulta cardiológica',
        ];

        foreach ($pacientes as $paciente) {
            $numConsultas = fake()->numberBetween(1, 3);

            for ($j = 0; $j < $numConsultas; $j++) {
                $fecha = Carbon::now()->subDays(random_int(0, 180))->addDays(random_int(0, 60))->format('Y-m-d');
                $hora = fake()->time('H:i:s');
                $especialidad = $especialidades->random();
                $medico = $medicos->where('especialidad_id', $especialidad->id)->first() ?? $medicos->random();

                $consulta = Consulta::create([
                    'user_id' => $paciente->id,
                    'medico_id' => $medico->id,
                    'especialidad_id' => $especialidad->id,
                    'fecha' => $fecha,
                    'hora' => $hora,
                    'motivo' => $motivos[array_rand($motivos)],
                    'estado' => $estados[array_rand($estados)],
                ]);

                // Crear historial clínico para consultas completadas o confirmadas
                if (in_array($consulta->estado, ['completada', 'confirmada']) && fake()->boolean(70)) {
                    HistorialClinico::create([
                        'consulta_id' => $consulta->id,
                        'paciente_id' => $paciente->id,
                        'medico_id' => $medico->id,
                        'nota' => fake()->paragraph(),
                        'diagnostico' => fake()->sentence(),
                        'tratamiento' => fake()->sentence(),
                    ]);
                }
            }
        }
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
                $horaInicio = fake()->time('H:i:s', '08:00:00');
                $horaFin = fake()->time('H:i:s', '17:00:00');

                // Asegurar que hora_fin sea posterior a hora_inicio
                if ($horaFin <= $horaInicio) {
                    $horaFin = '17:00:00';
                }

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

    private function crearNotificaciones(): void
    {
        $pacientes = User::where('role_id', Role::where('slug', 'paciente')->first()->id)
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
            'Tu solicitud de consulta ha sido recibida.',
            'Tu consulta ha sido confirmada por el médico.',
            'Tu consulta ha sido completada.',
            'Tienes una nueva cita programada.',
            'Recordatorio de consulta médica.',
        ];

        foreach ($pacientes as $paciente) {
            DB::table('notifications')->insert([
                'id' => Str::uuid(),
                'type' => $tipos[array_rand($tipos)],
                'notifiable_type' => User::class,
                'notifiable_id' => $paciente->id,
                'data' => json_encode([
                    'message' => $mensajes[array_rand($mensajes)],
                    'consulta_id' => fake()->numberBetween(1, 100),
                ]),
                'read_at' => fake()->boolean(30) ? now() : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // También notificaciones para médicos
        if ($medicos->isNotEmpty()) {
            foreach ($medicos->take(5) as $medico) {
                if ($medico->user) {
                    DB::table('notifications')->insert([
                        'id' => Str::uuid(),
                        'type' => 'App\Notifications\ConsultaAsignada',
                        'notifiable_type' => User::class,
                        'notifiable_id' => $medico->user->id,
                        'data' => json_encode([
                            'message' => 'Tienes una nueva consulta asignada.',
                            'medico_id' => $medico->id,
                        ]),
                        'read_at' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
