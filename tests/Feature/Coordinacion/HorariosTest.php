<?php

namespace Tests\Feature\Coordinacion;

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

    public function test_formulario_editar_medico_permite_asignar_un_horario(): void
    {
        $roles = $this->crearRoles();
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);
        $medico = $this->crearMedicoConHorario();

        $this->actingAs($coordinator)
            ->get(route('coordinacion.medicos.editar', $medico))
            ->assertOk()
            ->assertSee(route('coordinacion.medicos.horarios.store', $medico), false)
            ->assertSee('name="return_to" value="edit"', false)
            ->assertSee('name="dia_semana"', false)
            ->assertSee('name="hora_inicio"', false)
            ->assertSee('name="hora_fin"', false);

        $this->actingAs($coordinator)
            ->post(route('coordinacion.medicos.horarios.store', $medico), [
                'dia_semana' => 1,
                'hora_inicio' => '09:00',
                'hora_fin' => '12:00',
                'activo' => true,
                'return_to' => 'edit',
            ])
            ->assertRedirectToRoute('coordinacion.medicos.editar', $medico)
            ->assertSessionHas('success', 'Horario creado correctamente.');

        $this->assertDatabaseHas('horarios', [
            'medico_id' => $medico->id,
            'dia_semana' => 1,
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
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
        ]);

        $response = $this->actingAs($coordinator)->post(route('coordinacion.medicos.horarios.store', $medico), [
            'dia_semana' => 1,
            'hora_inicio' => '10:00',
            'hora_fin' => '11:00',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('horario');
        $this->assertDatabaseCount('horarios', 1);
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
}
