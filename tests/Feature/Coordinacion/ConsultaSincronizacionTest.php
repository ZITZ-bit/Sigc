<?php

namespace Tests\Feature\Coordinacion;

use App\Models\Consulta;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultaSincronizacionTest extends TestCase
{
    use RefreshDatabase;

    private Role $rolPaciente;

    private Role $rolMedico;

    private Role $rolCoordinador;

    private Especialidad $especialidad;

    private Medico $medicoRecord;

    private User $pacienteUsuario;

    private User $medicoUsuario;

    private User $coordinadorUsuario;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rolPaciente = Role::firstOrCreate(['slug' => 'paciente'], ['name' => 'Paciente']);
        $this->rolMedico = Role::firstOrCreate(['slug' => 'medico'], ['name' => 'Médico']);
        $this->rolCoordinador = Role::firstOrCreate(['slug' => 'coordinador'], ['name' => 'Coordinador']);

        $this->especialidad = Especialidad::create([
            'name' => 'Medicina General',
            'slug' => 'medicina-general',
        ]);

        $this->pacienteUsuario = User::create([
            'name' => 'Paciente Prueba',
            'email' => 'paciente.sincronizacion@sigc.test',
            'password' => bcrypt('password'),
            'role_id' => $this->rolPaciente->id,
        ]);

        $this->medicoUsuario = User::create([
            'name' => 'Médico Prueba',
            'email' => 'medico.sincronizacion@sigc.test',
            'password' => bcrypt('password'),
            'role_id' => $this->rolMedico->id,
        ]);

        $this->medicoRecord = Medico::create([
            'user_id' => $this->medicoUsuario->id,
            'especialidad_id' => $this->especialidad->id,
            'estado' => 'activo',
        ]);

        $this->coordinadorUsuario = User::create([
            'name' => 'Coordinador Prueba',
            'email' => 'coordinador.sincronizacion@sigc.test',
            'password' => bcrypt('password'),
            'role_id' => $this->rolCoordinador->id,
        ]);
    }

    /** @test */
    public function coordinador_ve_consulta_nueva_de_paciente_en_sincronizacion(): void
    {
        $consulta = $this->crearConsulta();

        $this->actingAs($this->coordinadorUsuario);
        $consultas = $this->sincronizar();

        $this->assertContains($consulta->id, array_column($consultas, 'id'));
    }

    /** @test */
    public function paciente_no_ve_consultas_de_otros_pacientes(): void
    {
        $otroPaciente = User::create([
            'name' => 'Otro Paciente',
            'email' => 'otro.paciente@sigc.test',
            'password' => bcrypt('password'),
            'role_id' => $this->rolPaciente->id,
        ]);
        $propia = $this->crearConsulta(['user_id' => $this->pacienteUsuario->id]);
        Consulta::create([
            'user_id' => $otroPaciente->id,
            'medico_id' => null,
            'especialidad_id' => $this->especialidad->id,
            'fecha' => '2026-11-05',
            'hora' => '11:00:00',
            'estado' => 'pendiente',
        ]);

        $this->actingAs($this->pacienteUsuario);
        $consultas = $this->sincronizar();

        $ids = array_column($consultas, 'id');
        $this->assertContains($propia->id, $ids);
        $this->assertCount(1, $consultas);
    }

    /** @test */
    public function medico_ve_solo_sus_consultas_asignadas(): void
    {
        $asignada = $this->crearConsulta(['medico_id' => $this->medicoRecord->id, 'estado' => 'confirmada']);
        Consulta::create([
            'user_id' => $this->pacienteUsuario->id,
            'medico_id' => 99999,
            'especialidad_id' => $this->especialidad->id,
            'fecha' => '2026-11-05',
            'hora' => '11:00:00',
            'estado' => 'confirmada',
        ]);

        $this->actingAs($this->medicoUsuario);
        $consultas = $this->sincronizar();

        $ids = array_column($consultas, 'id');
        $this->assertContains($asignada->id, $ids);
        $this->assertCount(1, $consultas);
    }

    /** @test */
    public function coordinador_ve_todas_las_consultas(): void
    {
        $this->crearConsulta();
        $this->crearConsulta(['medico_id' => $this->medicoRecord->id, 'estado' => 'confirmada']);

        $this->actingAs($this->coordinadorUsuario);
        $consultas = $this->sincronizar();

        $this->assertGreaterThanOrEqual(2, count($consultas));
    }

    /** @test */
    public function sincronizacion_incremental_solo_devuelve_consultas_cambiadas_despues_de_since(): void
    {
        $consulta = $this->crearConsulta();
        $antes = now()->subMinutes(5)->toDateTimeString();

        $this->actingAs($this->coordinadorUsuario);
        $primera = $this->sincronizar($antes);

        $this->assertContains($consulta->id, array_column($primera, 'id'));

        // Sin cambios después de ahora, la segunda sincronización debe estar vacía.
        $despues = now()->toDateTimeString();
        $segunda = $this->sincronizar($despues);
        $this->assertSame([], $segunda);
    }

    /** @test */
    public function cambio_de_estado_de_coordinacion_se_refleja_en_sincronizacion_de_paciente_y_medico(): void
    {
        $consulta = $this->crearConsulta([
            'medico_id' => $this->medicoRecord->id,
            'estado' => 'confirmada',
        ]);

        $this->actingAs($this->coordinadorUsuario);
        $this->post(route('coordinacion.citas.estado', $consulta), ['estado' => 'completada'])
            ->assertRedirect();

        // El paciente ve el cambio sin recargar.
        $this->actingAs($this->pacienteUsuario);
        $consultasPaciente = $this->sincronizar();
        $this->assertSame('completada', collect($consultasPaciente)->firstWhere('id', $consulta->id)['estado']);

        // El médico ve el cambio.
        $this->actingAs($this->medicoUsuario);
        $consultasMedico = $this->sincronizar();
        $this->assertSame('completada', collect($consultasMedico)->firstWhere('id', $consulta->id)['estado']);
    }

    /** @test */
    public function medico_completa_consulta_y_se_refleja_en_paciente_y_coordinador(): void
    {
        $consulta = $this->crearConsulta([
            'medico_id' => $this->medicoRecord->id,
            'estado' => 'confirmada',
        ]);

        $this->actingAs($this->medicoUsuario);
        $this->post(route('medico.consultas.completar', $consulta))
            ->assertRedirect();

        // Paciente ve la consulta como completada.
        $this->actingAs($this->pacienteUsuario);
        $consultasPaciente = $this->sincronizar();
        $this->assertSame('completada', collect($consultasPaciente)->firstWhere('id', $consulta->id)['estado']);

        // Coordinador ve la consulta como completada.
        $this->actingAs($this->coordinadorUsuario);
        $consultasCoordinador = $this->sincronizar();
        $this->assertSame('completada', collect($consultasCoordinador)->firstWhere('id', $consulta->id)['estado']);
    }

    /** @test */
    public function paciente_cancela_consulta_y_coordinador_lo_ve_en_sincronizacion(): void
    {
        $consulta = $this->crearConsulta(['estado' => 'confirmada']);

        $this->actingAs($this->pacienteUsuario);
        $this->post(route('paciente.consultas.cancelar', $consulta))->assertRedirect();

        $this->actingAs($this->coordinadorUsuario);
        $consultas = $this->sincronizar();
        $this->assertSame('cancelada', collect($consultas)->firstWhere('id', $consulta->id)['estado']);
    }

    /** @test */
    public function sincronizacion_requiere_autenticacion(): void
    {
        $response = $this->getJson('/consultas/sincronizar');
        $response->assertStatus(302); // redirige al login al no estar autenticado
    }

    /** @test */
    public function no_se_duplican_consultas_en_sincronizaciones_sucesivas(): void
    {
        $consulta = $this->crearConsulta(['medico_id' => $this->medicoRecord->id, 'estado' => 'confirmada']);

        $this->actingAs($this->coordinadorUsuario);
        $primera = $this->sincronizar(null);

        $this->assertContains($consulta->id, array_column($primera, 'id'));

        // Una sincronización con since igual o posterior al updated_at de la consulta no debe repetirla.
        $desdeActualizada = $primera[0]['updated_at'] ?? now()->toDateTimeString();
        $segunda = $this->sincronizar($desdeActualizada);
        $this->assertNotContains($consulta->id, array_column($segunda, 'id'));
    }

    private function crearConsulta(array $sobrescritos = []): Consulta
    {
        $atributos = array_merge([
            'user_id' => $this->pacienteUsuario->id,
            'medico_id' => null,
            'especialidad_id' => $this->especialidad->id,
            'fecha' => '2026-11-04',
            'hora' => '09:00:00',
            'motivo' => 'Prueba de sincronización',
            'estado' => 'pendiente',
        ], $sobrescritos);

        return Consulta::create($atributos);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function sincronizar(?string $since = null): array
    {
        $url = '/consultas/sincronizar';
        if ($since !== null) {
            $url .= '?since='.urlencode($since);
        }

        $respuesta = $this->getJson($url);
        $respuesta->assertStatus(200);

        $contenido = $respuesta->decodeResponseJson();

        return $contenido['consultas'] ?? [];
    }
}
