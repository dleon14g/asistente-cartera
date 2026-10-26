<?php

namespace Database\Factories;

use App\Models\Deudor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Deudor>
 */
class DeudorFactory extends Factory
{
    /**
     * Instancia de Faker en español de Argentina, compartida entre llamadas
     * para que ->unique() funcione correctamente a lo largo del seeding.
     */
    protected static ?\Faker\Generator $fakerAr = null;

    protected function fakerAr(): \Faker\Generator
    {
        return static::$fakerAr ??= \Faker\Factory::create('es_AR');
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = $this->fakerAr();

        $estado = $faker->randomElement(['al_dia', 'en_mora', 'en_gestion', 'incobrable']);

        return [
            'nombre' => $faker->name(),
            'dni' => $faker->unique()->numerify('########'),
            'provincia' => $faker->state(),
            'deuda' => $faker->randomFloat(2, 1000, 500000),
            'dias_mora' => $estado === 'al_dia' ? 0 : $faker->numberBetween(1, 365),
            'estado' => $estado,
        ];
    }
}
