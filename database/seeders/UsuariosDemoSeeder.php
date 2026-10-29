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
     *
     * Todo el seeder es idempotente: usa firstOrCreate y verificaciones de
     * existencia para evitar duplicados en lanzamientos repetidos. Los datos
     * clínicos (consultas, historiales, notificaciones) se generan de forma
     * determinística a partir de índices para que las claves de búsqueda
     * coincidan en cada ejecución.
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
                    'numero_licencia' => "LIC-DEMO-{$i}",
                    'telefono' => '+1-555-000-'.str_pad($i, 4, '0', STR_PAD_LEFT),
                    'fecha_incorporacion' => Carbon::create(2024, 1, 15)->addDays($i)->format('Y-m-d'),
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

        $especialidadesArray = $especialidades->values()->all();
        $departamentosArray = $departamentos->values()->all();

        Medico::all()->each(function ($medico) use ($especialidadesArray, $departamentosArray) {
            $indice = $medico->id % count($especialidadesArray);
            $medico->update([
                'especialidad_id' => $especialidadesArray[$indice]->id,
                'departamento_id' => $departamentosArray[$medico->id % count($departamentosArray)]->id,
            ]);
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

        foreach ($pacientes as $indicePaciente => $paciente) {
            $numConsultas = ($indicePaciente % 3) + 1; // 1, 2 o 3 consultas por paciente

            for ($j = 0; $j < $numConsultas; $j++) {
                // Fecha determinística: base + días del paciente + semanas de la consulta
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

                // Crear historial clínico para consultas completadas o confirmadas
                if (in_array($estado, ['completada', 'confirmada'])) {
                    HistorialClinico::firstOrCreate(
                        ['consulta_id' => $consulta->id],
                        [
                            'paciente_id' => $paciente->id,
                            'medico_id' => $medico->id,
                            'nota' => 'Nota clínica de demostración para paciente '.$paciente->id.' consulta '.($j + 1),
                            'diagnostico' => 'Diagnóstico de demostración '.($j + 1),
                            'tratamiento' => 'Tratamiento de demostración '.($j + 1),
                        ]
                    );
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
            'Tu solicitud de consulta ha sido recibida.',
            'Tu consulta ha sido confirmada por el médico.',
            'Tu consulta ha sido completada.',
            'Tienes una nueva cita programada.',
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

        // También notificaciones para médicos
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
                            'data' => json_encode(['message' => 'Tienes una nueva consulta asignada.']),
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
