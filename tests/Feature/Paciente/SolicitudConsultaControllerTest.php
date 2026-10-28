<?php

namespace Tests\Feature\Paciente;

use App\Models\Consulta;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Role;
use App\Models\User;
use App\Notifications\ConsultaSolicitada;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SolicitudConsultaControllerTest extends TestCase
{
    use RefreshDatabase;

    private function crearRoles(): array
    {
        return [
            'paciente' => Role::create(['name' => 'Paciente', 'slug' => 'paciente']),
            'medico' => Role::create(['name' => 'Médico', 'slug' => 'medico']),
            'coordinador' => Role::create(['name' => 'Coordinador', 'slug' => 'coordinador']),
        ];
    }

    private function crearMedicoDisponible(Especialidad $especialidad, int $diaSemana = 1): Medico
    {
        $role = Role::firstOrCreate(['slug' => 'medico'], ['name' => 'Médico']);
        $medicoUser = User::factory()->medico()->create(['role_id' => $role->id]);
        $medico = Medico::create([
            'user_id' => $medicoUser->id,
            'especialidad_id' => $especialidad->id,
            'estado' => 'activo',
        ]);
        $medico->horarios()->create([
            'dia_semana' => $diaSemana,
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'activo' => true,
        ]);

        return $medico;
    }

    public function test_paciente_ve_solo_horas_cubiertas_por_un_horario_activo(): void
    {
        $this->travelTo('2026-10-28 08:00:00');
        $roles = $this->crearRoles();
        $paciente = User::factory()->paciente()->create(['role_id' => $roles['paciente']->id]);
        $especialidad = Especialidad::create(['name' => 'Cardiología', 'slug' => 'cardiologia']);
        $this->crearMedicoDisponible($especialidad);

        $response = $this->withoutVite()
            ->actingAs($paciente)
            ->get(route('paciente.consulta', [
                'especialidad' => $especialidad->slug,
                'fecha' => '2026-11-02',
            ]));

        $response->assertOk()
            ->assertSee('Ver horarios')
            ->assertSee('09:00')
            ->assertSee('10:00')
            ->assertSee('11:00')
            ->assertSee('Describe qué te pasa o qué síntomas tienes')
            ->assertSee('Profesional')
            ->assertSee('Asignado por coordinación')
            ->assertSee('Enviar solicitud de consulta')
            ->assertDontSee('12:00</span>')
            ->assertDontSee('14:00</span>');
    }

    public function test_paciente_ve_horas_futuras_de_hoy_en_la_zona_horaria_local(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-10-28 17:30:00', 'America/Santo_Domingo'));
        $roles = $this->crearRoles();
        $paciente = User::factory()->paciente()->create(['role_id' => $roles['paciente']->id]);
        $especialidad = Especialidad::create(['name' => 'Cardiología', 'slug' => 'cardiologia']);
        $medico = $this->crearMedicoDisponible($especialidad, 3);
        $medico->horarios()->firstOrFail()->update([
            'hora_inicio' => '00:00',
            'hora_fin' => '23:59',
        ]);

        $response = $this->withoutVite()
            ->actingAs($paciente)
            ->get(route('paciente.consulta', [
                'especialidad' => $especialidad->slug,
                'fecha' => '2026-10-28',
            ]));

        $response->assertOk()
            ->assertSee('18:00')
            ->assertSee('19:00')
            ->assertDontSee('17:00</span>')
            ->assertDontSee('16:00</span>');

        $storeResponse = $this->actingAs($paciente)->post(route('paciente.consulta.store'), [
            'especialidad' => $especialidad->slug,
            'fecha' => '2026-10-28',
            'hora' => '18:00',
        ]);

        $storeResponse->assertRedirectToRoute('paciente.agenda');
        $this->assertDatabaseHas('consultas', [
            'user_id' => $paciente->id,
            'especialidad_id' => $especialidad->id,
            'fecha' => '2026-10-28',
            'hora' => '18:00',
            'estado' => 'pendiente',
        ]);
    }

    public function test_no_ofrece_horas_de_un_horario_que_ya_termino_hoy(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-10-28 17:23:00', 'America/Santo_Domingo'));
        $roles = $this->crearRoles();
        $paciente = User::factory()->paciente()->create(['role_id' => $roles['paciente']->id]);
        $especialidad = Especialidad::create(['name' => 'Medicina General', 'slug' => 'medicina-general']);
        $medico = $this->crearMedicoDisponible($especialidad, 3);
        $medico->horarios()->firstOrFail()->update([
            'hora_inicio' => '01:00',
            'hora_fin' => '12:00',
        ]);

        $response = $this->withoutVite()
            ->actingAs($paciente)
            ->get(route('paciente.consulta', [
                'especialidad' => $especialidad->slug,
                'fecha' => '2026-10-28',
            ]));

        $response->assertOk()
            ->assertSee('No hay horas disponibles para esta fecha')
            ->assertSee('Si el horario terminó al mediodía, ya no quedan horas para reservar.')
            ->assertDontSee('01:00</span>')
            ->assertDontSee('11:00</span>');
    }

    public function test_paciente_ve_el_formulario_y_el_boton_de_envio_aunque_no_haya_horas(): void
    {
        $this->travelTo('2026-10-28 08:00:00');
        $roles = $this->crearRoles();
        $paciente = User::factory()->paciente()->create(['role_id' => $roles['paciente']->id]);
        $especialidad = Especialidad::create(['name' => 'Cardiología', 'slug' => 'cardiologia']);

        $response = $this->withoutVite()
            ->actingAs($paciente)
            ->get(route('paciente.consulta', [
                'especialidad' => $especialidad->slug,
                'fecha' => '2026-11-02',
            ]));

        $response->assertOk()
            ->assertSee('Describe qué te pasa o qué síntomas tienes')
            ->assertSee('Enviar solicitud de consulta')
            ->assertSee('disabled', false)
            ->assertSee('No hay horas disponibles para esta fecha');
    }

    public function test_paciente_ve_horas_cuando_el_horario_del_medico_no_es_el_rango_predefinido(): void
    {
        $this->travelTo('2026-10-28 08:00:00');
        $roles = $this->crearRoles();
        $paciente = User::factory()->paciente()->create(['role_id' => $roles['paciente']->id]);
        $especialidad = Especialidad::create(['name' => 'Cardiología', 'slug' => 'cardiologia']);
        $medico = $this->crearMedicoDisponible($especialidad);
        $medico->horarios()->firstOrFail()->update([
            'hora_inicio' => '13:00',
            'hora_fin' => '16:00',
        ]);

        $response = $this->withoutVite()
            ->actingAs($paciente)
            ->get(route('paciente.consulta', [
                'especialidad' => $especialidad->slug,
                'fecha' => '2026-11-02',
            ]));

        $response->assertOk()
            ->assertSee('13:00')
            ->assertSee('14:00')
            ->assertSee('15:00')
            ->assertDontSee('12:00</span>');
    }

    public function test_paciente_puede_enviar_solicitud_en_horario_disponible(): void
    {
        $this->travelTo('2026-10-28 08:00:00');
        $roles = $this->crearRoles();
        $paciente = User::factory()->paciente()->create(['role_id' => $roles['paciente']->id]);
        $coordinador = User::factory()->coordinador()->create(['role_id' => $roles['coordinador']->id]);
        $especialidad = Especialidad::create(['name' => 'Cardiología', 'slug' => 'cardiologia']);
        $this->crearMedicoDisponible($especialidad);

        $response = $this->actingAs($paciente)->post(route('paciente.consulta.store'), [
            'especialidad' => $especialidad->slug,
            'fecha' => '2026-11-02',
            'hora' => '10:00',
            'motivo' => 'Consulta de seguimiento',
        ]);

        $response->assertRedirectToRoute('paciente.agenda');
        $this->assertDatabaseHas('consultas', [
            'user_id' => $paciente->id,
            'especialidad_id' => $especialidad->id,
            'medico_id' => null,
            'fecha' => '2026-11-02',
            'hora' => '10:00',
            'estado' => 'pendiente',
            'motivo' => 'Consulta de seguimiento',
        ]);

        $notification = $coordinador->notifications()->firstOrFail();
        $this->assertSame('Hay una nueva solicitud de consulta pendiente de revisión.', $notification->data['message']);
        $this->assertSame('Cardiología', $notification->data['especialidad']);
        $this->assertSame('2026-11-02', $notification->data['fecha']);
        $this->assertSame('10:00', $notification->data['hora']);

        $notificationPage = $this->withoutVite()
            ->actingAs($coordinador)
            ->get(route('coordinacion.notificaciones'));

        $notificationPage->assertOk()
            ->assertSee('data-notification-toast', false)
            ->assertSee(route('coordinacion.notificaciones.nuevas'), false)
            ->assertSee('Hay una nueva solicitud de consulta pendiente de revisión.')
            ->assertSee('Cardiología')
            ->assertSee($paciente->name)
            ->assertSee('lunes 02 de noviembre de 2026');

        $this->withoutVite()
            ->get(route('coordinacion.dashboard'))
            ->assertOk()
            ->assertDontSee('data-notification-toast', false);

        $coordinador->notify(new ConsultaSolicitada(Consulta::firstOrFail()));
        $pollResponse = $this->get(route('coordinacion.notificaciones.nuevas'));
        $pollResponse
            ->assertOk()
            ->assertJsonCount(1, 'notifications')
            ->assertJsonPath('notifications.0.title', 'Hay una nueva solicitud de consulta pendiente de revisión.');
        $this->assertDatabaseHas('notifications', [
            'id' => $pollResponse->json('notifications.0.id'),
            'read_at' => null,
        ]);
    }

    public function test_paciente_no_puede_enviar_solicitud_fuera_de_la_disponibilidad(): void
    {
        $this->travelTo('2026-10-28 08:00:00');
        $roles = $this->crearRoles();
        $paciente = User::factory()->paciente()->create(['role_id' => $roles['paciente']->id]);
        $especialidad = Especialidad::create(['name' => 'Cardiología', 'slug' => 'cardiologia']);
        $this->crearMedicoDisponible($especialidad);

        $response = $this->actingAs($paciente)->post(route('paciente.consulta.store'), [
            'especialidad' => $especialidad->slug,
            'fecha' => '2026-11-02',
            'hora' => '12:00',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors([
            'hora' => 'Ese horario ya no está disponible. Elige otra hora disponible.',
        ]);
        $this->assertDatabaseCount('consultas', 0);
    }

    public function test_paciente_no_ve_horas_ocupadas_si_no_hay_otro_medico_disponible(): void
    {
        $this->travelTo('2026-10-28 08:00:00');
        $roles = $this->crearRoles();
        $paciente = User::factory()->paciente()->create(['role_id' => $roles['paciente']->id]);
        $otroPaciente = User::factory()->paciente()->create(['role_id' => $roles['paciente']->id]);
        $especialidad = Especialidad::create(['name' => 'Cardiología', 'slug' => 'cardiologia']);
        $medico = $this->crearMedicoDisponible($especialidad);
        Consulta::create([
            'user_id' => $otroPaciente->id,
            'medico_id' => $medico->id,
            'especialidad_id' => $especialidad->id,
            'fecha' => '2026-11-02',
            'hora' => '09:00',
            'estado' => 'confirmada',
        ]);

        $response = $this->withoutVite()
            ->actingAs($paciente)
            ->get(route('paciente.consulta', [
                'especialidad' => $especialidad->slug,
                'fecha' => '2026-11-02',
            ]));

        $response->assertOk()
            ->assertDontSee('09:00</span>')
            ->assertSee('10:00');
    }
}
