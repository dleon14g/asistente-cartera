<?php

namespace Database\Factories;

use App\Models\Deudor;
use App\Models\Pago;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pago>
 */
class PagoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'deudor_id' => Deudor::factory(),
            'monto' => $this->faker->randomFloat(2, 500, 50000),
            'fecha' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
        ];
    }
}
