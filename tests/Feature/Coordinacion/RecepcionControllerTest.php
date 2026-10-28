<?php

namespace Tests\Feature\Coordinacion;

use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecepcionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_consultation_request_is_visible_to_a_coordinator_by_default(): void
    {
        $this->travelTo('2026-10-27 08:00:00');
        $this->withoutVite();

        $patientRole = Role::create(['name' => 'Paciente', 'slug' => 'paciente']);
        $coordinatorRole = Role::create(['name' => 'Coordinador', 'slug' => 'coordinador']);
        $patient = User::factory()->create([
            'name' => 'Paciente de prueba',
            'role_id' => $patientRole->id,
        ]);
        $coordinator = User::factory()->create([
            'name' => 'Coordinador de prueba',
            'role_id' => $coordinatorRole->id,
        ]);
        $specialty = Especialidad::create([
            'name' => 'Cardiología',
            'slug' => 'cardiologia',
        ]);
        $medicoRole = Role::create(['name' => 'Médico', 'slug' => 'medico']);
        $medicoUser = User::factory()->medico()->create(['role_id' => $medicoRole->id]);
        $medico = Medico::create([
            'user_id' => $medicoUser->id,
            'especialidad_id' => $specialty->id,
            'estado' => 'activo',
        ]);
        $medico->horarios()->create([
            'dia_semana' => 4,
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'activo' => true,
        ]);

        $response = $this->actingAs($patient)->post(route('paciente.consulta.store'), [
            'especialidad' => $specialty->slug,
            'fecha' => '2026-10-29',
            'hora' => '10:00',
            'motivo' => 'Consulta de seguimiento',
        ]);

        $response->assertRedirectToRoute('paciente.agenda');
        $this->assertDatabaseHas('consultas', [
            'user_id' => $patient->id,
            'especialidad_id' => $specialty->id,
            'fecha' => '2026-10-29',
            'hora' => '10:00',
            'estado' => 'pendiente',
            'motivo' => 'Consulta de seguimiento',
        ]);

        $response = $this->actingAs($coordinator)->get(route('coordinacion.recepcion'));

        $response->assertOk()
            ->assertSee('Paciente de prueba')
            ->assertSee('Cardiología')
            ->assertSee('Pendiente')
            ->assertSee('Todas');
    }
}
