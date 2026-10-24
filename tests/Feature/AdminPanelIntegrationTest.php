<?php

namespace Tests\Feature;

use App\Models\Clinica;
use App\Models\Consulta;
use App\Models\Especialidad;
use App\Models\Horario;
use App\Models\Invite;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPanelIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Clinica $clinic;

    private Especialidad $specialty;

    private User $patientUser;

    private Paciente $patient;

    private User $doctorUser;

    private Medico $doctor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->clinic = Clinica::create(['nombre' => 'Clínica de pruebas']);
        $this->specialty = Especialidad::create(['nombre' => 'Cardiología']);
        $this->admin = $this->createUser('admin@example.test', 'administrador');
        $this->patientUser = $this->createUser('patient@example.test', 'paciente');
        $this->patient = Paciente::create([
            'user_id' => $this->patientUser->id,
            'expediente_numero' => 'EXP-001',
        ]);
        $this->doctorUser = $this->createUser('doctor@example.test', 'medico');
        $this->doctor = Medico::create([
            'user_id' => $this->doctorUser->id,
            'clinica_id' => $this->clinic->id,
            'especialidad_id' => $this->specialty->id,
            'consultorio' => '03',
        ]);
    }

    public function test_admin_panel_is_protected_and_loads_database_records(): void
    {
        $this->get(route('admin.panel'))->assertRedirect(route('login'));

        $this->actingAs($this->admin)
            ->get(route('admin.panel'))
            ->assertOk()
            ->assertSee('patient@example.test')
            ->assertSee('doctor@example.test')
            ->assertSee('Cardiología')
            ->assertDontSee('Elena Vargas');
    }

    public function test_non_admin_cannot_open_admin_panel(): void
    {
        $this->actingAs($this->patientUser)
            ->get(route('admin.panel'))
            ->assertForbidden();
    }

    public function test_admin_can_update_patient_profile_and_account_status(): void
    {
        $this->actingAs($this->admin)
            ->put(route('admin.pacientes.update', $this->patient), [
                'name' => 'Patricia',
                'apellido' => 'Pruebas',
                'email' => 'patricia@example.test',
                'telefono' => '5550102030',
            ])
            ->assertRedirect(route('admin.panel', ['view' => 'pacientes']));

        $this->assertDatabaseHas('users', [
            'id' => $this->patientUser->id,
            'name' => 'Patricia',
            'apellido' => 'Pruebas',
            'email' => 'patricia@example.test',
            'telefono' => '5550102030',
        ]);

        $this->patch(route('admin.pacientes.estado', $this->patient), ['activo' => false])
            ->assertRedirect(route('admin.panel', ['view' => 'pacientes']));

        $this->assertDatabaseHas('users', ['id' => $this->patientUser->id, 'activo' => false]);
    }

    public function test_admin_can_invite_doctor_and_get_activation_link(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.medicos.invitar'), [
            'email' => 'newdoctor@example.test',
            'clinica_id' => $this->clinic->id,
            'especialidad_id' => $this->specialty->id,
        ]);

        $response->assertRedirect(route('admin.panel', ['view' => 'equipo']));
        $this->assertDatabaseHas('invites', [
            'email' => 'newdoctor@example.test',
            'role_id' => Role::where('nombre', 'medico')->value('id'),
        ]);
        $this->assertStringContainsString('/invitacion/', session('invite_url'));
    }

    public function test_admin_can_invite_patient_and_acceptance_creates_patient_profile(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.pacientes.invitar'), [
            'email' => 'newpatient@example.test',
        ]);
        $response->assertRedirect(route('admin.panel', ['view' => 'pacientes']));

        $invite = Invite::where('email', 'newpatient@example.test')->firstOrFail();
        $this->assertSame('paciente', $invite->role->nombre);
        $this->assertStringContainsString('/invitacion/' . $invite->token, session('invite_url'));

        $this->post(route('invite.accept', $invite->token), [
            'name' => 'Paciente',
            'apellido' => 'Invitado',
            'password' => 'patient-password',
            'password_confirmation' => 'patient-password',
        ])->assertRedirect(route('paciente.panel'));

        $createdUser = User::where('email', 'newpatient@example.test')->firstOrFail();
        $this->assertTrue($createdUser->hasRole('paciente'));
        $this->assertDatabaseHas('pacientes', ['user_id' => $createdUser->id]);
        $this->assertNotNull($invite->fresh()->accepted_at);
    }

    public function test_admin_can_create_specialty_and_schedule(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.especialidades.store'), ['nombre' => 'Neurología'])
            ->assertRedirect(route('admin.panel', ['view' => 'equipo']));

        $this->assertDatabaseHas('especialidades', ['nombre' => 'Neurología']);

        $this->post(route('admin.horarios.store'), [
            'medico_id' => $this->doctor->id,
            'dia_semana' => 'Lunes',
            'hora_inicio' => '08:00',
            'hora_fin' => '14:00',
            'duracion_minutos' => 30,
        ])->assertRedirect(route('admin.panel', ['view' => 'consultas', 'tab' => 'hours']));

        $this->assertDatabaseHas('horarios', [
            'medico_id' => $this->doctor->id,
            'dia_semana' => 'Lunes',
            'activo' => true,
        ]);
    }

    public function test_admin_can_create_consultation_only_inside_doctor_availability(): void
    {
        Horario::create([
            'medico_id' => $this->doctor->id,
            'dia_semana' => 'Lunes',
            'hora_inicio' => '08:00',
            'hora_fin' => '14:00',
            'duracion_minutos' => 30,
            'activo' => true,
        ]);

        $this->actingAs($this->admin)->post(route('admin.consultas.store'), [
            'paciente_id' => $this->patient->id,
            'medico_id' => $this->doctor->id,
            'fecha_hora' => '2026-11-02 09:00:00',
            'motivo' => 'Control de prueba',
        ])->assertRedirect(route('admin.panel', ['view' => 'consultas']));

        $this->assertDatabaseHas('consultas', [
            'paciente_id' => $this->patient->id,
            'medico_id' => $this->doctor->id,
            'estado' => 'confirmada',
        ]);

        $this->from(route('admin.panel'))->post(route('admin.consultas.store'), [
            'paciente_id' => $this->patient->id,
            'medico_id' => $this->doctor->id,
            'fecha_hora' => '2026-11-02 09:15:00',
            'motivo' => 'Consulta solapada',
        ])->assertSessionHasErrors('fecha_hora');

        $this->assertSame(1, Consulta::count());
    }

    private function createUser(string $email, string $roleName): User
    {
        $user = User::create([
            'name' => ucfirst(strstr($email, '@', true)),
            'apellido' => 'Prueba',
            'email' => $email,
            'password' => Hash::make('password-test'),
            'activo' => true,
        ]);
        $user->assignRole(Role::where('nombre', $roleName)->firstOrFail());

        return $user;
    }
}
