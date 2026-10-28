<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role_id' => Role::where('slug', 'paciente')->first()?->id ?? 1,
            'remember_token' => null,
        ];
    }

    /**
     * Indicate that the user is a patient.
     */
    public function paciente(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role_id' => Role::where('slug', 'paciente')->first()?->id ?? 1,
            ];
        });
    }

    /**
     * Indicate that the user is a doctor.
     */
    public function medico(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role_id' => Role::where('slug', 'medico')->first()?->id ?? 2,
            ];
        });
    }

    /**
     * Indicate that the user is a coordinator.
     */
    public function coordinador(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role_id' => Role::where('slug', 'coordinador')->first()?->id ?? 3,
            ];
        });
    }
}
