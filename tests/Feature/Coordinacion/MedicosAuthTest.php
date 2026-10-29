<?php

namespace Tests\Feature\Coordinacion;

use App\Models\Consulta;
use App\Models\HistorialClinico;
use App\Models\Invitacion;
use App\Models\Medico;
use App\Models\Role;
use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MedicosAuthTest extends TestCase
{
    use RefreshDatabase;

    private function crearRoles(): array
    {
        return [
            'medico' => Role::create(['name' => 'Médico', 'slug' => 'medico']),
            'coordinador' => Role::create(['name' => 'Coordinador', 'slug' => 'coordinador']),
            'paciente' => Role::create(['name' => 'Paciente', 'slug' => 'paciente']),
        ];
    }

    public function test_coordinador_autorizado_puede_crear_medico_y_existe_relacion_usuario_medico(): void
    {
        $roles = $this->crearRoles();
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);

        $response = $this->actingAs($coordinator)->post(route('coordinacion.medicos.store'), [
            'name' => 'Carlos',
            'apellido' => 'Méndez',
            'email' => 'carlos.mendez@clinica.test',
            'estado_inicial' => 'activo',
        ]);

        $response->assertRedirectToRoute('coordinacion.medicos');

        $user = User::where('email', 'carlos.mendez@clinica.test')->first();

        $this->assertNotNull($user);
        $this->assertSame($roles['medico']->id, $user->role_id);
        $this->assertInstanceOf(Medico::class, $user->medico);
        $this->assertSame($user->id, $user->medico->user_id);
        $this->assertSame('activo', $user->medico->estado);
        $this->assertDatabaseHas('invitaciones', [
            'medico_id' => $user->medico->id,
            'email' => 'carlos.mendez@clinica.test',
        ]);
    }

    public function test_resend_invitation_uses_the_configured_local_app_url(): void
    {
        $roles = $this->crearRoles();
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);
        $medicoUser = User::factory()->medico()->create([
            'role_id' => $roles['medico']->id,
        ]);
        $medico = Medico::create([
            'user_id' => $medicoUser->id,
            'estado' => 'pendiente',
        ]);

        config(['app.url' => 'http://localhost:8000']);
        (new AppServiceProvider($this->app))->boot();

        $response = $this->actingAs($coordinator)
            ->post('http://127.0.0.1/coordinacion/medicos/'.$medico->id.'/invitacion');

        $response->assertRedirectToRoute('coordinacion.medicos');
        $response->assertSessionHas('invitacion_url', function (mixed $url): bool {
            return is_string($url)
                && str_starts_with($url, 'http://localhost:8000/medico/activar/');
        });

        $activationUrl = session('invitacion_url');
        $this->assertIsString($activationUrl);
        $this->get($activationUrl)
            ->assertOk()
            ->assertViewIs('auth.activar')
            ->assertSee('Activa tu cuenta');
    }

    public function test_production_invitation_uses_app_url_and_activates_the_associated_medic(): void
    {
        $roles = $this->crearRoles();
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);

        config(['app.url' => 'https://sigc-ikoc.onrender.com']);
        (new AppServiceProvider($this->app))->boot();

        $response = $this->actingAs($coordinator)
            ->post('http://127.0.0.1/coordinacion/medicos', [
                'name' => 'Ana',
                'apellido' => 'Méndez',
                'email' => 'ana.mendez@clinica.test',
                'estado_inicial' => 'pendiente',
            ]);

        $response->assertRedirectToRoute('coordinacion.medicos');
        $response->assertSessionHas('invitacion_url');

        $activationUrl = session('invitacion_url');
        $this->assertIsString($activationUrl);
        $activationUri = parse_url($activationUrl);
        $this->assertIsArray($activationUri);
        $this->assertSame('https://sigc-ikoc.onrender.com', $activationUri['scheme'].'://'.$activationUri['host']);
        $this->assertStringStartsWith('/medico/activar/', $activationUri['path'] ?? '');

        $activationToken = basename($activationUri['path']);
        $medicoUser = User::where('email', 'ana.mendez@clinica.test')->firstOrFail();
        $medico = $medicoUser->medico;

        $this->assertSame($roles['medico']->id, $medicoUser->role_id);
        $this->assertSame($medicoUser->id, $medico->user_id);
        $this->assertDatabaseHas('invitaciones', [
            'medico_id' => $medico->id,
            'email' => $medicoUser->email,
            'token_hash' => hash('sha256', $activationToken),
        ]);

        $this->get($activationUrl)
            ->assertOk()
            ->assertViewIs('auth.activar')
            ->assertSee('Activa tu cuenta')
            ->assertSee('Nueva contraseña');

        $this->post($activationUrl, [
            'password' => 'NuevaContrasena123!',
            'password_confirmation' => 'NuevaContrasena123!',
        ])->assertRedirectToRoute('medico.dashboard');

        $this->assertTrue(Hash::check('NuevaContrasena123!', $medicoUser->fresh()->password));
        $this->assertSame('activo', $medico->fresh()->estado);
        $this->assertSame($roles['medico']->id, $medicoUser->fresh()->role_id);
        $this->assertNotNull($medico->invitacion->fresh()->used_at);

        $this->post($activationUrl, [
            'password' => 'OtraContrasena123!',
            'password_confirmation' => 'OtraContrasena123!',
        ])->assertRedirectToRoute('medico.activar', ['token' => $activationToken])
            ->assertSessionHasErrors('token');
    }

    public function test_medico_no_puede_iniciar_sesion_antes_de_activar_la_contrasena(): void
    {
        // Causa raíz: el usuario del médico se crea con una contraseña aleatoria desconocida
        // (app/Http/Controllers/Coordinacion/MedicosController.php:109), por lo que
        // Auth::attempt falla aunque el médico tenga estado 'activo'.
        $roles = $this->crearRoles();
        $user = User::factory()->medico()->create([
            'email' => 'carlos.mendez@clinica.test',
            'password' => Hash::make('contraseña-desconocida-por-el-medico'),
        ]);
        Medico::create([
            'user_id' => $user->id,
            'estado' => 'activo',
        ]);

        $this->assertSame('activo', $user->medico->estado);

        $response = $this->post(route('login.post'), [
            'email' => 'carlos.mendez@clinica.test',
            'password' => 'CualquierContrasena123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('password');

        // Tras el intento fallido, el usuario no queda autenticado.
        $this->get(route('medico.dashboard'))->assertRedirectToRoute('login');
    }

    public function test_medico_puede_iniciar_sesion_despues_de_activar_la_contrasena(): void
    {
        $roles = $this->crearRoles();
        $user = User::factory()->medico()->create([
            'email' => 'ana.mendez@clinica.test',
            'password' => Hash::make('placeholder-aleatorio'),
        ]);
        Medico::create([
            'user_id' => $user->id,
            'estado' => 'activo',
        ]);

        $rawToken = 'tokentokentokentokentokentokentokentokentokentokentokentokentokento';
        $tokenHash = hash('sha256', $rawToken);
        Invitacion::create([
            'medico_id' => $user->medico->id,
            'email' => $user->email,
            'token' => $tokenHash,
            'token_hash' => $tokenHash,
            'expires_at' => now()->addDays(7),
        ]);

        // La activación establece una contraseña real (verificación de hash).
        $activate = $this->post(route('medico.activar.post', ['token' => $rawToken]), [
            'password' => 'NuevaContrasena123!',
            'password_confirmation' => 'NuevaContrasena123!',
        ]);
        $activate->assertRedirectToRoute('medico.dashboard');

        $this->assertTrue(Hash::check('NuevaContrasena123!', $user->fresh()->password));
        $this->assertSame('activo', $user->medico->fresh()->estado);
        $this->assertNotNull($user->medico->invitacion->used_at);

        // Login válido con la contraseña recién establecida.
        $login = $this->post(route('login.post'), [
            'email' => 'ana.mendez@clinica.test',
            'password' => 'NuevaContrasena123!',
        ]);
        $login->assertRedirectToRoute('medico.dashboard');

        // La sesión persiste la autenticación en una petición siguiente.
        $this->get(route('medico.dashboard'))->assertOk();
    }

    public function test_login_rechaza_contrasena_incorrecta(): void
    {
        $roles = $this->crearRoles();
        $user = User::factory()->medico()->create([
            'email' => 'ana.mendez@clinica.test',
            'password' => Hash::make('ContrasenaCorrecta123!'),
        ]);
        Medico::create([
            'user_id' => $user->id,
            'estado' => 'activo',
        ]);

        $response = $this->post(route('login.post'), [
            'email' => 'ana.mendez@clinica.test',
            'password' => 'ContrasenaIncorrecta123!',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('password');

        $this->get(route('medico.dashboard'))->assertRedirectToRoute('login');
        $this->assertFalse(Hash::check('ContrasenaIncorrecta123!', $user->fresh()->password));
    }

    public function test_rol_y_permisos_de_rutas_medico(): void
    {
        $roles = $this->crearRoles();

        $medicoUser = User::factory()->medico()->create([
            'role_id' => $roles['medico']->id,
        ]);
        Medico::create([
            'user_id' => $medicoUser->id,
            'estado' => 'activo',
        ]);

        // Un médico autenticado accede a su dashboard.
        $this->actingAs($medicoUser)->get(route('medico.dashboard'))->assertOk();

        // Un paciente no puede acceder a rutas de médico (Role middleware redirige).
        $paciente = User::factory()->paciente()->create([
            'role_id' => $roles['paciente']->id,
        ]);
        $this->actingAs($paciente)->get(route('medico.dashboard'))->assertRedirect();

        // Un coordinador accede a sus propias rutas.
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);
        $this->actingAs($coordinator)->get(route('coordinacion.dashboard'))->assertOk();
    }

    public function test_coordinador_puede_eliminar_medico_sin_registros_clinicos(): void
    {
        $roles = $this->crearRoles();
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);
        $medicoUser = User::factory()->medico()->create([
            'role_id' => $roles['medico']->id,
        ]);
        $medico = Medico::create([
            'user_id' => $medicoUser->id,
            'estado' => 'inactivo',
        ]);

        $response = $this->actingAs($coordinator)
            ->delete(route('coordinacion.medicos.remove', $medico));

        $response->assertRedirectToRoute('coordinacion.medicos');
        $response->assertSessionHas('success', 'Médico eliminado correctamente.');
        $this->assertDatabaseMissing('medicos', ['id' => $medico->id]);
        $this->assertDatabaseMissing('users', ['id' => $medicoUser->id]);
    }

    public function test_no_se_puede_eliminar_medico_con_historial_clinico(): void
    {
        $roles = $this->crearRoles();
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);
        $medicoUser = User::factory()->medico()->create([
            'role_id' => $roles['medico']->id,
        ]);
        $medico = Medico::create([
            'user_id' => $medicoUser->id,
            'estado' => 'activo',
        ]);
        $patient = User::factory()->paciente()->create([
            'role_id' => $roles['paciente']->id,
        ]);
        $consulta = Consulta::create([
            'user_id' => $patient->id,
            'medico_id' => $medico->id,
            'fecha' => today(),
            'hora' => '09:00',
            'estado' => 'completada',
        ]);
        $historial = HistorialClinico::create([
            'consulta_id' => $consulta->id,
            'paciente_id' => $patient->id,
            'medico_id' => $medico->id,
        ]);

        $response = $this->actingAs($coordinator)
            ->delete(route('coordinacion.medicos.remove', $medico));

        $response->assertRedirectToRoute('coordinacion.medicos');
        $response->assertSessionHasErrors('medico');
        $this->assertDatabaseHas('medicos', ['id' => $medico->id]);
        $this->assertDatabaseHas('users', ['id' => $medicoUser->id]);
        $this->assertDatabaseHas('historial_clinico', ['id' => $historial->id]);
    }

    public function test_no_se_puede_eliminar_medico_con_consultas_propias(): void
    {
        $roles = $this->crearRoles();
        $coordinator = User::factory()->coordinador()->create([
            'role_id' => $roles['coordinador']->id,
        ]);
        $medicoUser = User::factory()->medico()->create([
            'role_id' => $roles['medico']->id,
        ]);
        $medico = Medico::create([
            'user_id' => $medicoUser->id,
            'estado' => 'activo',
        ]);
        Consulta::create([
            'user_id' => $medicoUser->id,
            'fecha' => today(),
            'hora' => '09:00',
            'estado' => 'pendiente',
        ]);

        $response = $this->actingAs($coordinator)
            ->delete(route('coordinacion.medicos.remove', $medico));

        $response->assertRedirectToRoute('coordinacion.medicos');
        $response->assertSessionHasErrors('medico');
        $this->assertDatabaseHas('medicos', ['id' => $medico->id]);
        $this->assertDatabaseHas('users', ['id' => $medicoUser->id]);
        $this->assertDatabaseHas('consultas', ['user_id' => $medicoUser->id]);
    }
}
