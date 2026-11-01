<?php

namespace Tests\Feature\Coordinacion;

use App\Models\Consulta;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

    public function test_coordinador_ve_consulta_nueva_de_paciente_en_sincronizacion(): void
    {
        $consulta = $this->crearConsulta();

        $this->actingAs($this->coordinadorUsuario);
        $consultas = $this->sincronizar();

        $this->assertContains($consulta->id, array_column($consultas, 'id'));
    }

    public function test_paciente_no_ve_consultas_de_otros_pacientes(): void
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

    public function test_medico_ve_solo_sus_consultas_asignadas(): void
    {
        $asignada = $this->crearConsulta(['medico_id' => $this->medicoRecord->id, 'estado' => 'confirmada']);
        $otroMedicoUser = User::factory()->medico()->create(['role_id' => $this->rolMedico->id]);
        $otroMedico = Medico::create([
            'user_id' => $otroMedicoUser->id,
            'especialidad_id' => $this->especialidad->id,
            'estado' => 'activo',
        ]);
        Consulta::create([
            'user_id' => $this->pacienteUsuario->id,
            'medico_id' => $otroMedico->id,
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

    public function test_coordinador_ve_todas_las_consultas(): void
    {
        $this->crearConsulta();
        $this->crearConsulta(['medico_id' => $this->medicoRecord->id, 'estado' => 'confirmada']);

        $this->actingAs($this->coordinadorUsuario);
        $consultas = $this->sincronizar();

        $this->assertGreaterThanOrEqual(2, count($consultas));
    }

    public function test_sincronizacion_incremental_solo_devuelve_consultas_cambiadas_despues_de_since(): void
    {
        $consulta = $this->crearConsulta();
        $antes = now()->subMinutes(5)->toDateTimeString();

        $this->actingAs($this->coordinadorUsuario);
        $primera = $this->sincronizar($antes);

        $this->assertContains($consulta->id, array_column($primera, 'id'));

        // Sin cambios después de ahora, la segunda sincronización debe estar vacía.
        // since es estrictamente posterior al updated_at de la consulta, así que no se devuelve.
        $despues = Carbon::parse($consulta->updated_at)->addSecond()->toDateTimeString();
        $segunda = $this->sincronizar($despues);
        $this->assertSame([], $segunda);
    }

    public function test_cambio_de_estado_de_coordinacion_se_refleja_en_sincronizacion_de_paciente_y_medico(): void
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

    public function test_medico_completa_consulta_y_se_refleja_en_paciente_y_coordinador(): void
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

    public function test_paciente_cancela_consulta_y_coordinador_lo_ve_en_sincronizacion(): void
    {
        $consulta = $this->crearConsulta(['estado' => 'confirmada']);

        $this->actingAs($this->pacienteUsuario);
        $this->post(route('paciente.consultas.cancelar', $consulta))->assertRedirect();

        $this->actingAs($this->coordinadorUsuario);
        $consultas = $this->sincronizar();
        $this->assertSame('cancelada', collect($consultas)->firstWhere('id', $consulta->id)['estado']);
    }

    public function test_sincronizacion_requiere_autenticacion(): void
    {
        $response = $this->getJson('/consultas/sincronizar');
        $response->assertStatus(302); // redirige al login al no estar autenticado
    }

    public function test_no_se_duplican_consultas_en_sincronizaciones_sucesivas(): void
    {
        $consulta = $this->crearConsulta(['medico_id' => $this->medicoRecord->id, 'estado' => 'confirmada']);

        $this->actingAs($this->coordinadorUsuario);
        $primera = $this->sincronizar(null);

        $this->assertContains($consulta->id, array_column($primera, 'id'));

        // Una sincronización con since estrictamente posterior al updated_at no debe repetirla.
        $desdeActualizada = Carbon::parse($consulta->updated_at)->addSecond()->toDateTimeString();
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

    public function test_sincronizacion_incremental_no_pierde_registros_con_timestamp_igual(): void
    {
        $consulta = $this->crearConsulta();
        $desdeActualizada = $consulta->updated_at->toDateTimeString();

        $this->actingAs($this->coordinadorUsuario);

        // Con since igual al updated_at de la consulta, no debe perderse (usa >=).
        $sincronizada = $this->sincronizar($desdeActualizada);
        $this->assertContains($consulta->id, array_column($sincronizada, 'id'));
    }

    public function test_paciente_no_puede_marcar_notificacion_de_otro_usuario_como_leida(): void
    {
        $otroPaciente = User::create([
            'name' => 'Otro Paciente',
            'email' => 'otro.paciente.notif@sigc.test',
            'password' => bcrypt('password'),
            'role_id' => $this->rolPaciente->id,
        ]);

        $notification = DatabaseNotification::create([
            'id' => (string) Str::uuid(),
            'type' => 'App\Notifications\ConsultaEstadoActualizado',
            'notifiable_type' => User::class,
            'notifiable_id' => $otroPaciente->id,
            'data' => json_encode(['message' => 'Notificación ajena']),
            'read_at' => null,
        ]);
        $notificacionId = $notification->id;

        $this->actingAs($this->pacienteUsuario)
            ->post(route('paciente.notificaciones.leer', $notificacionId), [], [
                'X-XSRF-TOKEN' => 'token-invalido',
            ])
            ->assertStatus(404);

        // La notificación del otro usuario no debe haberse modificado.
        $this->assertDatabaseHas('notifications', [
            'id' => $notificacionId,
            'read_at' => null,
        ]);
    }

    /** @test */
    public function test_paciente_puede_marcar_su_propia_notificacion_como_leida(): void
    {
        $notification = DatabaseNotification::create([
            'id' => (string) Str::uuid(),
            'type' => 'App\Notifications\ConsultaEstadoActualizado',
            'notifiable_type' => User::class,
            'notifiable_id' => $this->pacienteUsuario->id,
            'data' => json_encode(['message' => 'Notificación propia']),
            'read_at' => null,
        ]);
        $notificacionId = $notification->id;

        $this->actingAs($this->pacienteUsuario)
            ->post(route('paciente.notificaciones.leer', $notificacionId), [], [
                'X-XSRF-TOKEN' => 'cualquier-token',
            ])
            ->assertStatus(302);

        // La notificación debe haberse marcado como leída.
        $this->assertNotNull(
            DB::table('notifications')->where('id', $notificacionId)->value('read_at')
        );
    }
}
