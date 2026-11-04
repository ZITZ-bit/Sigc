<?php

namespace Tests\Feature;

use App\Models\Consulta;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FiltrosVivosTest extends TestCase
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

    private function crearUsuario(array $roles, string $roleSlug, string $name, string $email): User
    {
        return User::factory()->create([
            'name' => $name,
            'email' => $email,
            'role_id' => $roles[$roleSlug]->id,
        ]);
    }

    private function ajax(User $user, string $uri, array $params = [])
    {
        $query = count($params) > 0 ? '?'.http_build_query($params) : '';

        return $this->withoutVite()
            ->actingAs($user)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get($uri.$query);
    }

    private function crearConsulta(User $paciente, array $attributes = []): Consulta
    {
        return Consulta::create(array_merge([
            'user_id' => $paciente->id,
            'fecha' => '2026-11-02',
            'hora' => '09:00',
            'estado' => 'pendiente',
        ], $attributes));
    }

    /* ------------------------------------------------------------
     * Coordinación - Citas
     * ------------------------------------------------------------ */

    public function test_coordinador_ajax_devuelve_parcial_sin_html_completo(): void
    {
        $roles = $this->crearRoles();
        $coordinador = $this->crearUsuario($roles, 'coordinador', 'Coordinador', 'coord@sigc.test');
        $paciente = $this->crearUsuario($roles, 'paciente', 'Ana García', 'ana@sigc.test');
        $this->crearConsulta($paciente);

        $response = $this->ajax($coordinador, route('coordinacion.citas'));

        $response->assertOk()
            ->assertSee('Ana García')
            ->assertDontSee('<!DOCTYPE html', false)
            ->assertDontSee('<html', false);
    }

    public function test_coordinador_busca_citas_por_una_letra_sin_distinguir_mayusculas(): void
    {
        $roles = $this->crearRoles();
        $coordinador = $this->crearUsuario($roles, 'coordinador', 'Coordinador', 'coord@sigc.test');
        $paciente = $this->crearUsuario($roles, 'paciente', 'Ana García', 'ana@sigc.test');
        $otro = $this->crearUsuario($roles, 'paciente', 'Pedro López', 'pedro@sigc.test');
        $this->crearConsulta($paciente);
        $this->crearConsulta($otro);

        $response = $this->ajax($coordinador, route('coordinacion.citas'), ['search' => 'A']);

        $response->assertOk()
            ->assertSee('Ana García')
            ->assertDontSee('Pedro López');

        $response = $this->ajax($coordinador, route('coordinacion.citas'), ['search' => 'a']);

        $response->assertOk()->assertSee('Ana García')->assertDontSee('Pedro López');
    }

    public function test_coordinador_filtra_citas_por_estado(): void
    {
        $roles = $this->crearRoles();
        $coordinador = $this->crearUsuario($roles, 'coordinador', 'Coordinador', 'coord@sigc.test');
        $paciente = $this->crearUsuario($roles, 'paciente', 'Ana García', 'ana@sigc.test');
        $this->crearConsulta($paciente, ['estado' => 'pendiente']);
        $this->crearConsulta($paciente, ['estado' => 'completada', 'fecha' => '2026-11-03']);

        $response = $this->ajax($coordinador, route('coordinacion.citas'), ['estado' => 'completada']);

        $response->assertOk()
            ->assertSee('Completada')
            ->assertDontSee('data-consulta-estado="pendiente"', false);
    }

    public function test_coordinador_combina_busqueda_estado_y_fecha(): void
    {
        $roles = $this->crearRoles();
        $coordinador = $this->crearUsuario($roles, 'coordinador', 'Coordinador', 'coord@sigc.test');
        $paciente = $this->crearUsuario($roles, 'paciente', 'Ana García', 'ana@sigc.test');
        $this->crearConsulta($paciente, ['estado' => 'pendiente', 'fecha' => '2026-11-02']);
        $this->crearConsulta($paciente, ['estado' => 'pendiente', 'fecha' => '2026-11-03']);

        $response = $this->ajax($coordinador, route('coordinacion.citas'), [
            'search' => 'ana',
            'estado' => 'pendiente',
            'fecha' => '2026-11-02',
        ]);

        $response->assertOk()->assertSee('Ana García');
    }

    public function test_coordinador_al_limpiar_filtros_recupera_todas_las_citas(): void
    {
        $roles = $this->crearRoles();
        $coordinador = $this->crearUsuario($roles, 'coordinador', 'Coordinador', 'coord@sigc.test');
        $paciente = $this->crearUsuario($roles, 'paciente', 'Ana García', 'ana@sigc.test');
        $this->crearConsulta($paciente);

        $filtrado = $this->ajax($coordinador, route('coordinacion.citas'), ['search' => 'zzz-no-existe']);

        $filtrado->assertOk()->assertSee('No se encontraron citas');

        $todos = $this->ajax($coordinador, route('coordinacion.citas'));

        $todos->assertOk()->assertSee('Ana García')->assertDontSee('No se encontraron citas');
    }

    public function test_coordinador_cuando_no_hay_coincidencias_muestra_estado_vacio(): void
    {
        $roles = $this->crearRoles();
        $coordinador = $this->crearUsuario($roles, 'coordinador', 'Coordinador', 'coord@sigc.test');

        $response = $this->ajax($coordinador, route('coordinacion.citas'), ['search' => 'ninguno']);

        $response->assertOk()
            ->assertSee('No se encontraron citas')
            ->assertDontSee('coordinacion-cita-card', false);
    }

    public function test_coordinador_paginacion_ajax_conserva_filtros(): void
    {
        $roles = $this->crearRoles();
        $coordinador = $this->crearUsuario($roles, 'coordinador', 'Coordinador', 'coord@sigc.test');
        $paciente = $this->crearUsuario($roles, 'paciente', 'Paciente Paginación', 'paginacion@sigc.test');

        for ($i = 0; $i < 12; $i++) {
            $this->crearConsulta($paciente, ['fecha' => '2026-11-0'.(1 + ($i % 9))]);
        }

        $pagina1 = $this->ajax($coordinador, route('coordinacion.citas'), ['search' => 'Paginación']);

        $pagina1->assertOk()->assertSee('Paciente Paginación');

        $pagina2 = $this->ajax($coordinador, route('coordinacion.citas'), [
            'search' => 'Paginación',
            'page' => 2,
        ]);

        $pagina2->assertOk()->assertSee('Paciente Paginación');
    }

    /* ------------------------------------------------------------
     * Coordinación - Médicos
     * ------------------------------------------------------------ */

    public function test_coordinador_busca_medicos_por_una_letra(): void
    {
        $roles = $this->crearRoles();
        $coordinador = $this->crearUsuario($roles, 'coordinador', 'Coordinador', 'coord@sigc.test');
        $medicoUser = $this->crearUsuario($roles, 'medico', 'Laura Ríos', 'laura@sigc.test');
        Medico::create(['user_id' => $medicoUser->id, 'estado' => 'activo']);
        $otro = $this->crearUsuario($roles, 'medico', 'Sofía Torres', 'sofia@sigc.test');
        Medico::create(['user_id' => $otro->id, 'estado' => 'activo']);

        $response = $this->ajax($coordinador, route('coordinacion.medicos'), ['search' => 'L']);

        $response->assertOk()
            ->assertSee('Laura Ríos')
            ->assertDontSee('Sofía Torres');
    }

    public function test_coordinador_filtra_medicos_por_estado(): void
    {
        $roles = $this->crearRoles();
        $coordinador = $this->crearUsuario($roles, 'coordinador', 'Coordinador', 'coord@sigc.test');
        $activo = $this->crearUsuario($roles, 'medico', 'Activo Uno', 'activo@sigc.test');
        Medico::create(['user_id' => $activo->id, 'estado' => 'activo']);
        $inactivo = $this->crearUsuario($roles, 'medico', 'Inactivo Uno', 'inactivo@sigc.test');
        Medico::create(['user_id' => $inactivo->id, 'estado' => 'inactivo']);

        $response = $this->ajax($coordinador, route('coordinacion.medicos'), ['estado' => 'inactivo']);

        $response->assertOk()
            ->assertSee('Inactivo Uno')
            ->assertDontSee('Activo Uno');
    }

    /* ------------------------------------------------------------
     * Coordinación - Pacientes
     * ------------------------------------------------------------ */

    public function test_coordinador_busca_pacientes_por_una_letra(): void
    {
        $roles = $this->crearRoles();
        $coordinador = $this->crearUsuario($roles, 'coordinador', 'Coordinador', 'coord@sigc.test');
        $paciente = $this->crearUsuario($roles, 'paciente', 'María Pérez', 'maria@sigc.test');
        $otro = $this->crearUsuario($roles, 'paciente', 'Pedro Díaz', 'pedro@sigc.test');

        $response = $this->ajax($coordinador, route('coordinacion.pacientes'), ['search' => 'm']);

        $response->assertOk()
            ->assertSee('María Pérez')
            ->assertDontSee('Pedro Díaz');
    }

    /* ------------------------------------------------------------
     * Coordinación - Recepción
     * ------------------------------------------------------------ */

    public function test_coordinador_filtra_recepcion_por_periodo_ajax(): void
    {
        $roles = $this->crearRoles();
        $coordinador = $this->crearUsuario($roles, 'coordinador', 'Coordinador', 'coord@sigc.test');

        $this->travelTo('2026-10-27 08:00:00');
        $paciente = $this->crearUsuario($roles, 'paciente', 'Paciente Recepción', 'recepcion@sigc.test');
        $especialidad = Especialidad::create(['name' => 'Cardiología', 'slug' => 'cardiologia']);
        $medicoUser = $this->crearUsuario($roles, 'medico', 'Médico Recepción', 'medicor@sigc.test');
        $medico = Medico::create([
            'user_id' => $medicoUser->id,
            'especialidad_id' => $especialidad->id,
            'estado' => 'activo',
        ]);
        $medico->horarios()->create([
            'dia_semana' => 2,
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'activo' => true,
        ]);

        $this->actingAs($paciente)->post(route('paciente.consulta.store'), [
            'especialidad' => $especialidad->slug,
            'fecha' => '2026-10-27',
            'hora' => '10:00',
            'motivo' => 'Consulta de seguimiento',
        ]);

        $response = $this->ajax($coordinador, route('coordinacion.recepcion'), ['filtro' => 'hoy']);

        $response->assertOk()->assertSee('Paciente Recepción')->assertSee('Cardiología');
    }

    /* ------------------------------------------------------------
     * Médico - Agenda y Consultas
     * ------------------------------------------------------------ */

    public function test_medico_busca_consultas_de_agenda_por_una_letra(): void
    {
        $roles = $this->crearRoles();
        $medicoUser = $this->crearUsuario($roles, 'medico', 'Médico Agenda', 'agenda@sigc.test');
        $medico = Medico::create(['user_id' => $medicoUser->id, 'estado' => 'activo']);
        $paciente = $this->crearUsuario($roles, 'paciente', 'Paciente Agenda', 'agendap@sigc.test');
        $this->crearConsulta($paciente, ['medico_id' => $medico->id, 'estado' => 'pendiente']);
        $otro = $this->crearUsuario($roles, 'paciente', 'Benito Pérez', 'benito@sigc.test');
        $this->crearConsulta($otro, ['medico_id' => $medico->id, 'estado' => 'pendiente']);

        $response = $this->ajax($medicoUser, route('medico.agenda'), ['search' => 'A']);

        $response->assertOk()
            ->assertSee('Paciente Agenda')
            ->assertDontSee('Otro Paciente');
    }

    public function test_medico_filtra_agenda_por_estado(): void
    {
        $roles = $this->crearRoles();
        $medicoUser = $this->crearUsuario($roles, 'medico', 'Médico Agenda 2', 'agenda2@sigc.test');
        $medico = Medico::create(['user_id' => $medicoUser->id, 'estado' => 'activo']);
        $paciente = $this->crearUsuario($roles, 'paciente', 'Paciente P', 'pacp@sigc.test');
        $this->crearConsulta($paciente, ['medico_id' => $medico->id, 'estado' => 'completada']);
        $this->crearConsulta($paciente, ['medico_id' => $medico->id, 'estado' => 'pendiente', 'fecha' => '2026-11-05']);

        $response = $this->ajax($medicoUser, route('medico.agenda'), ['estado' => 'completada']);

        $response->assertOk()
            ->assertSee('Completada')
            ->assertDontSee('Pendiente');
    }

    public function test_medico_busca_consultas_por_una_letra(): void
    {
        $roles = $this->crearRoles();
        $medicoUser = $this->crearUsuario($roles, 'medico', 'Médico Consultas', 'consultas@sigc.test');
        $medico = Medico::create(['user_id' => $medicoUser->id, 'estado' => 'activo']);
        $paciente = $this->crearUsuario($roles, 'paciente', 'Paciente Consulta', 'consultap@sigc.test');
        $this->crearConsulta($paciente, ['medico_id' => $medico->id]);

        $response = $this->ajax($medicoUser, route('medico.consultas'), ['search' => 'C']);

        $response->assertOk()->assertSee('Paciente Consulta');
    }

    /* ------------------------------------------------------------
     * Médico - Pacientes
     * ------------------------------------------------------------ */

    public function test_medico_busca_sus_pacientes_por_una_letra(): void
    {
        $roles = $this->crearRoles();
        $medicoUser = $this->crearUsuario($roles, 'medico', 'Médico Pacientes', 'medpac@sigc.test');
        $medico = Medico::create(['user_id' => $medicoUser->id, 'estado' => 'activo']);
        $paciente = $this->crearUsuario($roles, 'paciente', 'Paciente del Médico', 'medpacp@sigc.test');
        $this->crearConsulta($paciente, ['medico_id' => $medico->id]);
        $otro = $this->crearUsuario($roles, 'paciente', 'Paciente Ajeno', 'ajeno@sigc.test');

        $response = $this->ajax($medicoUser, route('medico.pacientes'), ['search' => 'Médico']);

        $response->assertOk()
            ->assertSee('Paciente del Médico')
            ->assertDontSee('Paciente Ajeno');
    }

    /* ------------------------------------------------------------
     * Paciente - Doctores
     * ------------------------------------------------------------ */

    public function test_paciente_busca_doctores_por_una_letra(): void
    {
        $roles = $this->crearRoles();
        $paciente = $this->crearUsuario($roles, 'paciente', 'Paciente Doctores', 'doctoresp@sigc.test');
        $especialidad = Especialidad::create(['name' => 'Dermatología', 'slug' => 'dermatologia']);
        $medicoUser = $this->crearUsuario($roles, 'medico', 'Doctora Piel', 'piel@sigc.test');
        Medico::create([
            'user_id' => $medicoUser->id,
            'especialidad_id' => $especialidad->id,
            'estado' => 'activo',
        ]);
        $otro = $this->crearUsuario($roles, 'medico', 'Médico Ajeno', 'ajenomed@sigc.test');
        Medico::create(['user_id' => $otro->id, 'estado' => 'activo']);

        $response = $this->ajax($paciente, route('paciente.doctores'), ['search' => 'Piel']);

        $response->assertOk()
            ->assertSee('Doctora Piel')
            ->assertDontSee('Médico Ajeno');
    }
}
