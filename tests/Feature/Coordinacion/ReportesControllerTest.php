<?php

namespace Tests\Feature\Coordinacion;

use App\Models\Consulta;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportesControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_reportes_muestran_graficas_filtradas_por_fecha(): void
    {
        $this->travelTo('2026-10-29 12:00:00');
        $this->withoutVite();

        $pacienteRole = Role::create(['name' => 'Paciente', 'slug' => 'paciente']);
        $coordinadorRole = Role::create(['name' => 'Coordinador', 'slug' => 'coordinador']);
        $coordinador = User::factory()->create(['role_id' => $coordinadorRole->id]);
        $paciente = User::factory()->create(['role_id' => $pacienteRole->id]);

        Consulta::create([
            'user_id' => $paciente->id,
            'fecha' => '2026-08-03',
            'hora' => '09:00',
            'estado' => 'pendiente',
        ]);
        Consulta::create([
            'user_id' => $paciente->id,
            'fecha' => '2026-08-14',
            'hora' => '09:00',
            'estado' => 'confirmada',
        ]);
        Consulta::create([
            'user_id' => $paciente->id,
            'fecha' => '2026-10-08',
            'hora' => '09:00',
            'estado' => 'completada',
        ]);
        Consulta::create([
            'user_id' => $paciente->id,
            'fecha' => '2026-06-08',
            'hora' => '09:00',
            'estado' => 'cancelada',
        ]);

        $response = $this->actingAs($coordinador)->get(route('coordinacion.reportes', [
            'desde' => '2026-07-01',
            'hasta' => '2026-10-31',
        ]));

        $response->assertOk()
            ->assertSee('Consultas a lo largo del tiempo')
            ->assertSee('data-values="0,2,0,1"', false)
            ->assertSee('data-values="1,1,1,0"', false)
            ->assertSee('oct. 26')
            ->assertDontSee('Laravel');
    }

    public function test_reportes_rechazan_un_rango_de_fechas_invertido(): void
    {
        $coordinadorRole = Role::create(['name' => 'Coordinador', 'slug' => 'coordinador']);
        $coordinador = User::factory()->create(['role_id' => $coordinadorRole->id]);

        $response = $this->from(route('coordinacion.reportes'))
            ->actingAs($coordinador)
            ->get(route('coordinacion.reportes', [
                'desde' => '2026-10-31',
                'hasta' => '2026-10-01',
            ]));

        $response->assertRedirect(route('coordinacion.reportes'))
            ->assertSessionHasErrors('hasta');
    }

    public function test_reportes_permiten_filtrar_solo_por_fecha_final(): void
    {
        $coordinadorRole = Role::create(['name' => 'Coordinador', 'slug' => 'coordinador']);
        $coordinador = User::factory()->create(['role_id' => $coordinadorRole->id]);

        $response = $this->withoutVite()
            ->actingAs($coordinador)
            ->get(route('coordinacion.reportes', ['hasta' => '2026-10-29']));

        $response->assertOk()
            ->assertSee('Consultas a lo largo del tiempo');
    }
}
