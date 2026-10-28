<?php

namespace Database\Factories;

use App\Models\Consulta;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsultaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Consulta::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->paciente(),
            'medico_id' => null,
            'especialidad_id' => null,
            'fecha' => fake()->dateBetween('+1 day', '+30 days'),
            'hora' => '10:00:00',
            'motivo' => fake()->sentence(),
            'estado' => 'pendiente',
        ];
    }

    /**
     * Indicate que la consulta está confirmada y tiene médico asignado.
     */
    public function confirmada(): static
    {
        return $this->state([
            'estado' => 'confirmada',
        ]);
    }

    /**
     * Asigna la consulta a un paciente y especialidad concretos.
     */
    public function paraUsuario(User $usuario, int $especialidadId): static
    {
        return $this->state([
            'user_id' => $usuario->id,
            'especialidad_id' => $especialidadId,
        ]);
    }
}
