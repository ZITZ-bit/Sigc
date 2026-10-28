<?php

namespace Tests\Feature\Paciente;

use App\Models\Consulta;
use App\Models\HistorialClinico;
use App\Models\Medico;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultasControllerTest extends TestCase
{
    use RefreshDatabase;

    private function crearRoles(): array
    {
        return [
            'paciente' => Role::create(['name' => 'Paciente', 'slug' => 'paciente']),
            'medico' => Role::create(['name' => 'Médico', 'slug' => 'medico']),
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

    public function test_paciente_puede_cancelar_su_cita_pendiente(): void
    {
        $roles = $this->crearRoles();
        $paciente = User::factory()->paciente()->create(['role_id' => $roles['paciente']->id]);
        $consulta = $this->crearConsulta($paciente);

        $response = $this->actingAs($paciente)->post(route('paciente.consultas.cancelar', $consulta));

        $response->assertRedirectToRoute('paciente.agenda');
        $response->assertSessionHas('success', 'La cita se canceló correctamente.');
        $this->assertDatabaseHas('consultas', [
            'id' => $consulta->id,
            'estado' => 'cancelada',
        ]);
    }

    public function test_paciente_ve_acciones_de_cancelacion_y_eliminacion_en_su_agenda(): void
    {
        $roles = $this->crearRoles();
        $paciente = User::factory()->paciente()->create(['role_id' => $roles['paciente']->id]);
        $this->crearConsulta($paciente);

        $response = $this->withoutVite()
            ->actingAs($paciente)
            ->get(route('paciente.agenda'));

        $response->assertOk()
            ->assertSee('paciente-agenda-item', false)
            ->assertSee('Cancelar')
            ->assertSee('Eliminar');
    }

    public function test_paciente_puede_eliminar_su_cita(): void
    {
        $roles = $this->crearRoles();
        $paciente = User::factory()->paciente()->create(['role_id' => $roles['paciente']->id]);
        $consulta = $this->crearConsulta($paciente, 'cancelada');

        $response = $this->actingAs($paciente)->delete(route('paciente.consultas.destroy', $consulta));

        $response->assertRedirectToRoute('paciente.agenda');
        $response->assertSessionHas('success', 'La cita se eliminó correctamente.');
        $this->assertDatabaseMissing('consultas', ['id' => $consulta->id]);
    }

    public function test_paciente_no_puede_eliminar_cita_de_otro_paciente(): void
    {
        $roles = $this->crearRoles();
        $paciente = User::factory()->paciente()->create(['role_id' => $roles['paciente']->id]);
        $otroPaciente = User::factory()->paciente()->create(['role_id' => $roles['paciente']->id]);
        $consulta = $this->crearConsulta($otroPaciente);

        $this->actingAs($paciente)
            ->delete(route('paciente.consultas.destroy', $consulta))
            ->assertNotFound();

        $this->assertDatabaseHas('consultas', ['id' => $consulta->id]);
    }

    public function test_paciente_no_puede_eliminar_cita_con_historial_clinico(): void
    {
        $roles = $this->crearRoles();
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

        $response = $this->actingAs($paciente)
            ->delete(route('paciente.consultas.destroy', $consulta));

        $response->assertRedirect();
        $response->assertSessionHasErrors('consulta');
        $this->assertDatabaseHas('consultas', ['id' => $consulta->id]);
        $this->assertDatabaseHas('historial_clinico', ['id' => $historial->id]);
    }
}
