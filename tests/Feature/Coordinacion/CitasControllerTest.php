<?php

namespace Tests\Feature\Coordinacion;

use App\Models\Consulta;
use App\Models\Especialidad;
use App\Models\HistorialClinico;
use App\Models\Horario;
use App\Models\Medico;
use App\Models\Role;
use App\Models\User;
use App\Notifications\ConsultaEstadoActualizado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CitasControllerTest extends TestCase
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

    private function crearConsulta(User $paciente, string $estado = 'pendiente'): Consulta
    {
        return Consulta::create([
            'user_id' => $paciente->id,
            'fecha' => '2026-11-02',
            'hora' => '09:00',
            'estado' => $estado,
        ]);
    }

    public function test_coordinador_puede_eliminar_cita_sin_historial_clinico(): void
    {
        $roles = $this->crearRoles();
        $coordinador = User::factory()->coordinador()->create(['role_id' => $roles['coordinador']->id]);
        $paciente = User::factory()->paciente()->create(['role_id' => $roles['paciente']->id]);
        $consulta = $this->crearConsulta($paciente);

        $response = $this->actingAs($coordinador)->delete(route('coordinacion.citas.destroy', $consulta));

        $response->assertRedirectToRoute('coordinacion.citas');
        $response->assertSessionHas('success', 'La cita se eliminó correctamente.');
        $this->assertDatabaseMissing('consultas', ['id' => $consulta->id]);
    }

    public function test_coordinador_no_puede_eliminar_cita_con_historial_clinico(): void
    {
        $roles = $this->crearRoles();
        $coordinador = User::factory()->coordinador()->create(['role_id' => $roles['coordinador']->id]);
        $paciente = User::factory()->paciente()->create(['role_id' => $roles['paciente']->id]);
        $medicoUser = User::factory()->medico()->create(['role_id' => $roles['medico']->id]);
        $medico = Medico::create([
            'user_id' => $medicoUser->id,
            'estado' => 'activo',
        ]);
        $consulta = $this->crearConsulta($paciente, 'completada');
        $consulta->update(['medico_id' => $medico->id]);
        $historial = HistorialClinico::create([
            'consulta_id' => $consulta->id,
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'nota' => 'Seguimiento clínico',
        ]);

        $response = $this->actingAs($coordinador)
            ->delete(route('coordinacion.citas.destroy', $consulta));

        $response->assertRedirect();
        $response->assertSessionHasErrors('consulta');
        $this->assertDatabaseHas('consultas', ['id' => $consulta->id]);
        $this->assertDatabaseHas('historial_clinico', ['id' => $historial->id]);
    }

    public function test_coordinador_ve_citas_en_tarjetas_con_acciones(): void
    {
        $roles = $this->crearRoles();
        $coordinador = User::factory()->coordinador()->create(['role_id' => $roles['coordinador']->id]);
        $paciente = User::factory()->paciente()->create([
            'role_id' => $roles['paciente']->id,
            'name' => 'Paciente de prueba',
        ]);
        $this->crearConsulta($paciente);

        $response = $this->withoutVite()
            ->actingAs($coordinador)
            ->get(route('coordinacion.citas'));

        $response->assertOk()
            ->assertSee('coordinacion-cita-card', false)
            ->assertSee('Paciente de prueba')
            ->assertSee('Cancelar cita')
            ->assertSee('Eliminar cita');
    }

    public function test_coordinador_puede_asignar_medico_en_el_inicio_del_horario_activo(): void
    {
        $roles = $this->crearRoles();
        $coordinador = User::factory()->coordinador()->create(['role_id' => $roles['coordinador']->id]);
        $paciente = User::factory()->paciente()->create([
            'role_id' => $roles['paciente']->id,
            'name' => 'Paciente asignado',
        ]);
        $medicoUser = User::factory()->medico()->create([
            'role_id' => $roles['medico']->id,
            'name' => 'Médico asignado',
        ]);
        $especialidad = Especialidad::create(['name' => 'Medicina General', 'slug' => 'medicina-general']);
        $medico = Medico::create([
            'user_id' => $medicoUser->id,
            'especialidad_id' => $especialidad->id,
            'estado' => 'activo',
        ]);
        $consulta = $this->crearConsulta($paciente);
        $consulta->update(['especialidad_id' => $especialidad->id]);
        Horario::create([
            'medico_id' => $medico->id,
            'dia_semana' => 1,
            'hora_inicio' => '09:00:00',
            'hora_fin' => '12:00:00',
            'activo' => true,
        ]);

        $response = $this->actingAs($coordinador)->post(
            route('coordinacion.citas.asignar-medico', $consulta),
            ['medico_id' => $medico->id],
        );

        $response->assertRedirectToRoute('coordinacion.citas');
        $response->assertSessionHas('success', 'Médico asignado correctamente a la consulta.');
        $this->assertDatabaseHas('consultas', [
            'id' => $consulta->id,
            'medico_id' => $medico->id,
            'estado' => 'confirmada',
        ]);

        $notification = $medicoUser->notifications()->firstOrFail();
        $this->assertSame('Paciente asignado', $notification->data['paciente']);
        $this->assertSame('Medicina General', $notification->data['especialidad']);
        $this->assertSame('2026-11-02', $notification->data['fecha']);

        $notificationPage = $this->withoutVite()
            ->actingAs($medicoUser)
            ->get(route('medico.notificaciones'));
        $notificationPage->assertOk()
            ->assertSee('data-notification-toast', false)
            ->assertSee('Paciente asignado')
            ->assertSee('lunes 02 de noviembre de 2026');

        $this->withoutVite()
            ->get(route('medico.dashboard'))
            ->assertOk()
            ->assertDontSee('data-notification-toast', false);

        $patientNotification = $paciente->notifications()->firstOrFail();
        $this->assertSame('Tu consulta ha sido aceptada.', $patientNotification->data['message']);
        $this->assertSame('Médico asignado', $patientNotification->data['medico']);
        $this->assertSame('Medicina General', $patientNotification->data['especialidad']);
        $this->assertSame('2026-11-02', $patientNotification->data['fecha']);

        $patientNotificationPage = $this->withoutVite()
            ->actingAs($paciente)
            ->get(route('paciente.notificaciones'));
        $patientNotificationPage->assertOk()
            ->assertSee('data-notification-toast', false)
            ->assertSee(route('paciente.notificaciones.nuevas'), false)
            ->assertSee('Tu consulta ha sido aceptada.')
            ->assertSee('Médico asignado')
            ->assertSee('Medicina General')
            ->assertSee('lunes 02 de noviembre de 2026');

        $this->withoutVite()
            ->get(route('paciente.dashboard'))
            ->assertOk()
            ->assertDontSee('data-notification-toast', false);

        $paciente->notify(new ConsultaEstadoActualizado($consulta));
        $pollResponse = $this->get(route('paciente.notificaciones.nuevas'));
        $pollResponse
            ->assertOk()
            ->assertJsonCount(1, 'notifications')
            ->assertJsonPath('notifications.0.title', 'Tu consulta ha sido aceptada.');
        $this->assertDatabaseHas('notifications', [
            'id' => $pollResponse->json('notifications.0.id'),
            'read_at' => null,
        ]);

        $this->get(route('paciente.notificaciones.nuevas'))
            ->assertOk()
            ->assertJsonCount(0, 'notifications');
    }

    public function test_coordinador_no_puede_asignar_medico_en_el_final_del_horario_activo(): void
    {
        $roles = $this->crearRoles();
        $coordinador = User::factory()->coordinador()->create(['role_id' => $roles['coordinador']->id]);
        $paciente = User::factory()->paciente()->create(['role_id' => $roles['paciente']->id]);
        $medicoUser = User::factory()->medico()->create(['role_id' => $roles['medico']->id]);
        $medico = Medico::create([
            'user_id' => $medicoUser->id,
            'estado' => 'activo',
        ]);
        $consulta = $this->crearConsulta($paciente);
        $consulta->update(['hora' => '12:00']);
        Horario::create([
            'medico_id' => $medico->id,
            'dia_semana' => 1,
            'hora_inicio' => '09:00:00',
            'hora_fin' => '12:00:00',
            'activo' => true,
        ]);

        $response = $this->actingAs($coordinador)->post(
            route('coordinacion.citas.asignar-medico', $consulta),
            ['medico_id' => $medico->id],
        );

        $response->assertRedirect();
        $response->assertSessionHasErrors([
            'medico_id' => 'El médico no tiene un horario activo que cubra el lunes a las 12:00. Verifica que ese día esté activo y que la hora esté dentro del rango; la hora final no se incluye.',
        ]);
        $this->assertDatabaseHas('consultas', [
            'id' => $consulta->id,
            'medico_id' => null,
        ]);
    }

    public function test_paciente_recibe_notificacion_con_medico_y_fecha_al_aceptar_consulta(): void
    {
        $roles = $this->crearRoles();
        $coordinador = User::factory()->coordinador()->create(['role_id' => $roles['coordinador']->id]);
        $paciente = User::factory()->paciente()->create([
            'role_id' => $roles['paciente']->id,
            'name' => 'Paciente de prueba',
        ]);
        $medicoUser = User::factory()->medico()->create([
            'role_id' => $roles['medico']->id,
            'name' => 'Doctora Asignada',
        ]);
        $especialidad = Especialidad::create(['name' => 'Medicina General', 'slug' => 'medicina-general']);
        $medico = Medico::create([
            'user_id' => $medicoUser->id,
            'especialidad_id' => $especialidad->id,
            'estado' => 'activo',
        ]);
        $consulta = $this->crearConsulta($paciente);
        $consulta->update(['medico_id' => $medico->id, 'especialidad_id' => $especialidad->id]);
        Horario::create([
            'medico_id' => $medico->id,
            'dia_semana' => 1,
            'hora_inicio' => '09:00:00',
            'hora_fin' => '12:00:00',
            'activo' => true,
        ]);

        $response = $this->actingAs($coordinador)->post(
            route('coordinacion.citas.estado', $consulta),
            ['estado' => 'confirmada'],
        );

        $response->assertRedirectToRoute('coordinacion.citas');
        $this->assertDatabaseHas('consultas', [
            'id' => $consulta->id,
            'estado' => 'confirmada',
        ]);

        $notification = $paciente->notifications()->firstOrFail();
        $this->assertSame('Tu consulta ha sido aceptada.', $notification->data['message']);
        $this->assertSame('Doctora Asignada', $notification->data['medico']);
        $this->assertSame('Medicina General', $notification->data['especialidad']);
        $this->assertSame('2026-11-02', $notification->data['fecha']);
        $this->assertSame('09:00', substr($notification->data['hora'], 0, 5));

        $notificationPage = $this->actingAs($paciente)->get(route('paciente.notificaciones'));

        $notificationPage->assertOk()
            ->assertSee('Tu consulta ha sido aceptada.')
            ->assertSee('Doctora Asignada')
            ->assertSee('Medicina General')
            ->assertSee('lunes 02 de noviembre de 2026');

        $patientNotification = $paciente->notifications()->firstOrFail();
        $this->actingAs($paciente)->post(route('paciente.notificaciones.leer', $patientNotification->id));
        $this->assertNotNull($patientNotification->fresh()->read_at);

        $this->actingAs($coordinador)->post(route('coordinacion.citas.estado', $consulta), ['estado' => 'cancelada']);
        $this->assertSame(1, $paciente->notifications()->count());
    }
}
