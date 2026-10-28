<?php

namespace Tests\Feature\Coordinacion;

use App\Models\Consulta;
use App\Models\Medico;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HorariosTest extends TestCase
{
    use RefreshDatabase;

    private function crearRoles(): array
    {
        return [
            'medico' => Role::firstOrCreate(['slug' => 'medico'], ['name' => 'Médico']),
            'coordinador' => Role::firstOrCreate(['slug' => 'coordinador'], ['name' => 'Coordinador']),
            'paciente' => Role::firstOrCreate(['slug' => 'paciente'], ['name' => 'Paciente']),
        ];
    }

    private function crearMedicoConHorario(array $attributes = []): Medico
    {
        $roles = $this->crearRoles();
        $medicoUser = User::factory()->medico()->create([
            'role_id' => $roles['medico']->id,
            ...$attributes,
        ]);

        return Medico::create([
            'user_id' => $medicoUser->id,
            'estado' => 'activo',
        ]);
    }

    public function test_coordinador_puede_crear_horario_para_medico(): void
    {
        $roles = $this->crearRoles();
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);
        $medico = $this->crearMedicoConHorario();

        $response = $this->actingAs($coordinator)->post(route('coordinacion.medicos.horarios.store', $medico), [
            'dia_semana' => 1,
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'activo' => true,
        ]);

        $response->assertRedirectToRoute('coordinacion.medicos.horarios', $medico);
        $this->assertDatabaseHas('horarios', [
            'medico_id' => $medico->id,
            'dia_semana' => 1,
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'activo' => true,
        ]);
    }

    public function test_pantalla_horarios_permite_asignar_un_horario(): void
    {
        $roles = $this->crearRoles();
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);
        $medico = $this->crearMedicoConHorario();

        $this->actingAs($coordinator)
            ->get(route('coordinacion.medicos.horarios', $medico))
            ->assertOk()
            ->assertSee(route('coordinacion.medicos.horarios.store', $medico), false)
            ->assertSee('name="dia_semana"', false)
            ->assertSee('name="hora_inicio"', false)
            ->assertSee('name="hora_fin"', false);

        $this->actingAs($coordinator)
            ->post(route('coordinacion.medicos.horarios.store', $medico), [
                'dia_semana' => 1,
                'hora_inicio' => '09:00',
                'hora_fin' => '12:00',
                'activo' => true,
            ])
            ->assertRedirectToRoute('coordinacion.medicos.horarios', $medico)
            ->assertSessionHas('success', 'Horario creado correctamente.');

        $this->assertDatabaseHas('horarios', [
            'medico_id' => $medico->id,
            'dia_semana' => 1,
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'activo' => true,
        ]);
    }

    public function test_editar_medico_no_muestra_el_editor_de_horarios(): void
    {
        $roles = $this->crearRoles();
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);
        $medico = $this->crearMedicoConHorario();

        $this->actingAs($coordinator)
            ->get(route('coordinacion.medicos.editar', $medico))
            ->assertOk()
            ->assertSee('Editar médico')
            ->assertDontSee('Disponibilidad semanal')
            ->assertDontSee(route('coordinacion.medicos.horarios.store', $medico), false);
    }

    public function test_pantalla_horarios_muestra_y_permite_actualizar_horarios_en_tarjetas(): void
    {
        $roles = $this->crearRoles();
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);
        $medico = $this->crearMedicoConHorario();
        $horario = $medico->horarios()->create([
            'dia_semana' => 1,
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'activo' => true,
        ]);

        $this->actingAs($coordinator)
            ->get(route('coordinacion.medicos.horarios', $medico))
            ->assertOk()
            ->assertSee('Disponibilidad semanal')
            ->assertSee('Lunes')
            ->assertSee('09:00')
            ->assertSee(route('coordinacion.medicos.horarios.update', [$medico, $horario]), false)
            ->assertSee(route('coordinacion.medicos.horarios.eliminar', [$medico, $horario]), false)
            ->assertSee('Guardar horario');

        $this->actingAs($coordinator)->put(
            route('coordinacion.medicos.horarios.update', [$medico, $horario]),
            [
                'dia_semana' => 1,
                'hora_inicio' => '10:00',
                'hora_fin' => '13:00',
                'activo' => true,
            ],
        )->assertRedirectToRoute('coordinacion.medicos.horarios', $medico);

        $this->assertDatabaseHas('horarios', [
            'id' => $horario->id,
            'hora_inicio' => '10:00',
            'hora_fin' => '13:00',
            'activo' => true,
        ]);
    }

    public function test_horario_rechaza_solapamiento_mismo_medico_y_dia(): void
    {
        $roles = $this->crearRoles();
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);
        $medico = $this->crearMedicoConHorario();

        $this->actingAs($coordinator)->post(route('coordinacion.medicos.horarios.store', $medico), [
            'dia_semana' => 1,
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'activo' => true,
        ]);

        $response = $this->actingAs($coordinator)->post(route('coordinacion.medicos.horarios.store', $medico), [
            'dia_semana' => 1,
            'hora_inicio' => '10:00',
            'hora_fin' => '11:00',
            'activo' => true,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('horario');
        $this->assertDatabaseCount('horarios', 1);
    }

    public function test_horario_permite_rangos_contiguos_el_mismo_dia(): void
    {
        $roles = $this->crearRoles();
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);
        $medico = $this->crearMedicoConHorario();
        $medico->horarios()->create([
            'dia_semana' => 1,
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'activo' => true,
        ]);

        $response = $this->actingAs($coordinator)->post(route('coordinacion.medicos.horarios.store', $medico), [
            'dia_semana' => 1,
            'hora_inicio' => '12:00',
            'hora_fin' => '14:00',
            'activo' => true,
        ]);

        $response->assertRedirectToRoute('coordinacion.medicos.horarios', $medico);
        $this->assertDatabaseHas('horarios', [
            'medico_id' => $medico->id,
            'dia_semana' => 1,
            'hora_inicio' => '12:00',
            'hora_fin' => '14:00',
            'activo' => true,
        ]);
    }

    public function test_horario_inactivo_no_impide_crear_un_rango_activo_igual(): void
    {
        $roles = $this->crearRoles();
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);
        $medico = $this->crearMedicoConHorario();
        $medico->horarios()->create([
            'dia_semana' => 1,
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'activo' => false,
        ]);

        $response = $this->actingAs($coordinator)->post(route('coordinacion.medicos.horarios.store', $medico), [
            'dia_semana' => 1,
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'activo' => true,
        ]);

        $response->assertRedirectToRoute('coordinacion.medicos.horarios', $medico);
        $this->assertDatabaseHas('horarios', [
            'medico_id' => $medico->id,
            'dia_semana' => 1,
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'activo' => true,
        ]);
    }

    public function test_horario_inactivo_no_puede_activarse_si_se_solapa_con_otro_activo(): void
    {
        $roles = $this->crearRoles();
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);
        $medico = $this->crearMedicoConHorario();
        $medico->horarios()->create([
            'dia_semana' => 1,
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'activo' => true,
        ]);
        $horarioInactivo = $medico->horarios()->create([
            'dia_semana' => 1,
            'hora_inicio' => '10:00',
            'hora_fin' => '11:00',
            'activo' => false,
        ]);

        $response = $this->actingAs($coordinator)->put(
            route('coordinacion.medicos.horarios.update', [$medico, $horarioInactivo]),
            [
                'dia_semana' => 1,
                'hora_inicio' => '10:00',
                'hora_fin' => '11:00',
                'activo' => true,
            ],
        );

        $response->assertRedirect();
        $response->assertSessionHasErrors([
            'horario' => 'Este horario se cruza con otro horario activo del médico en ese día.',
        ]);
        $this->assertDatabaseHas('horarios', [
            'id' => $horarioInactivo->id,
            'activo' => false,
        ]);
    }

    public function test_horario_no_se_puede_mover_y_dejar_una_cita_confirmada_sin_cobertura(): void
    {
        $roles = $this->crearRoles();
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);
        $medico = $this->crearMedicoConHorario();
        $paciente = User::factory()->paciente()->create([
            'role_id' => $roles['paciente']->id,
        ]);
        $consulta = Consulta::create([
            'user_id' => $paciente->id,
            'medico_id' => $medico->id,
            'fecha' => '2026-11-02',
            'hora' => '09:00',
            'estado' => 'confirmada',
        ]);
        $horario = $medico->horarios()->create([
            'dia_semana' => 1,
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'activo' => true,
        ]);

        $response = $this->actingAs($coordinator)->put(
            route('coordinacion.medicos.horarios.update', [$medico, $horario]),
            [
                'dia_semana' => 1,
                'hora_inicio' => '10:00',
                'hora_fin' => '12:00',
                'activo' => true,
            ],
        );

        $response->assertRedirect();
        $response->assertSessionHasErrors('horario');
        $this->assertDatabaseHas('horarios', [
            'id' => $horario->id,
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'activo' => true,
        ]);
        $this->assertDatabaseHas('consultas', [
            'id' => $consulta->id,
            'estado' => 'confirmada',
        ]);
    }

    public function test_horario_rechaza_hora_fin_antes_de_inicio(): void
    {
        $roles = $this->crearRoles();
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);
        $medico = $this->crearMedicoConHorario();

        $response = $this->actingAs($coordinator)->post(route('coordinacion.medicos.horarios.store', $medico), [
            'dia_semana' => 1,
            'hora_inicio' => '12:00',
            'hora_fin' => '09:00',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('hora_fin');
    }

    public function test_horario_requiere_medico_existente(): void
    {
        $roles = $this->crearRoles();
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);

        $response = $this->actingAs($coordinator)->get(route('coordinacion.medicos.horarios', 99999));
        $response->assertNotFound();
    }

    public function test_no_coordinador_no_puede_acceder_horarios(): void
    {
        $roles = $this->crearRoles();
        $medico = $this->crearMedicoConHorario();
        $paciente = User::factory()->paciente()->create([
            'role_id' => $roles['paciente']->id,
        ]);

        $this->actingAs($paciente)->get(route('coordinacion.medicos.horarios', $medico))->assertRedirect();
    }

    public function test_horario_puede_editarse_y_desactivarse(): void
    {
        $roles = $this->crearRoles();
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);
        $medico = $this->crearMedicoConHorario();
        $horario = $medico->horarios()->create([
            'dia_semana' => 1,
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'activo' => true,
        ]);

        $this->actingAs($coordinator)->put(route('coordinacion.medicos.horarios.update', [$medico, $horario]), [
            'dia_semana' => 2,
            'hora_inicio' => '14:00',
            'hora_fin' => '17:00',
            'activo' => true,
        ])->assertRedirectToRoute('coordinacion.medicos.horarios', $medico);

        $this->assertDatabaseHas('horarios', [
            'id' => $horario->id,
            'dia_semana' => 2,
            'hora_inicio' => '14:00',
            'hora_fin' => '17:00',
            'activo' => true,
        ]);

        $this->actingAs($coordinator)->post(route('coordinacion.medicos.horarios.destroy', [$medico, $horario]))
            ->assertRedirectToRoute('coordinacion.medicos.horarios', $medico);

        $this->assertDatabaseHas('horarios', [
            'id' => $horario->id,
            'activo' => false,
        ]);
    }

    public function test_coordinador_puede_eliminar_horario_sin_citas_confirmadas_afectadas(): void
    {
        $roles = $this->crearRoles();
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);
        $medico = $this->crearMedicoConHorario();
        $horario = $medico->horarios()->create([
            'dia_semana' => 1,
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'activo' => true,
        ]);

        $response = $this->actingAs($coordinator)->delete(
            route('coordinacion.medicos.horarios.eliminar', [$medico, $horario]),
        );

        $response->assertRedirectToRoute('coordinacion.medicos.horarios', $medico);
        $response->assertSessionHas('success', 'Horario eliminado correctamente.');
        $this->assertDatabaseMissing('horarios', ['id' => $horario->id]);
    }

    public function test_coordinador_no_puede_eliminar_horario_si_deja_una_cita_confirmada_sin_cobertura(): void
    {
        $roles = $this->crearRoles();
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);
        $medico = $this->crearMedicoConHorario();
        $paciente = User::factory()->paciente()->create([
            'role_id' => $roles['paciente']->id,
        ]);
        $horario = $medico->horarios()->create([
            'dia_semana' => 1,
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'activo' => true,
        ]);
        Consulta::create([
            'user_id' => $paciente->id,
            'medico_id' => $medico->id,
            'fecha' => '2026-11-02',
            'hora' => '09:00',
            'estado' => 'confirmada',
        ]);

        $response = $this->actingAs($coordinator)->delete(
            route('coordinacion.medicos.horarios.eliminar', [$medico, $horario]),
        );

        $response->assertRedirect();
        $response->assertSessionHasErrors([
            'horario' => 'No se puede eliminar este horario porque dejaría citas confirmadas sin cobertura.',
        ]);
        $this->assertDatabaseHas('horarios', ['id' => $horario->id]);
    }

    public function test_coordinador_puede_eliminar_horario_cuando_otro_horario_cubre_la_cita(): void
    {
        $roles = $this->crearRoles();
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);
        $medico = $this->crearMedicoConHorario();
        $paciente = User::factory()->paciente()->create([
            'role_id' => $roles['paciente']->id,
        ]);
        $horarioAEliminar = $medico->horarios()->create([
            'dia_semana' => 1,
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'activo' => true,
        ]);
        $medico->horarios()->create([
            'dia_semana' => 1,
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'activo' => true,
        ]);
        Consulta::create([
            'user_id' => $paciente->id,
            'medico_id' => $medico->id,
            'fecha' => '2026-11-02',
            'hora' => '09:00',
            'estado' => 'confirmada',
        ]);

        $response = $this->actingAs($coordinator)->delete(
            route('coordinacion.medicos.horarios.eliminar', [$medico, $horarioAEliminar]),
        );

        $response->assertRedirectToRoute('coordinacion.medicos.horarios', $medico);
        $this->assertDatabaseMissing('horarios', ['id' => $horarioAEliminar->id]);
    }
}
