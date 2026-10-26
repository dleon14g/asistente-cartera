<?php

namespace Database\Seeders;

use App\Models\Deudor;
use App\Models\Pago;
use Illuminate\Database\Seeder;

class DeudorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Deudor::factory()
            ->count(200)
            ->create()
            ->each(function (Deudor $deudor) {
                $cantidadPagos = rand(0, 5);

                if ($cantidadPagos > 0) {
                    Pago::factory()
                        ->count($cantidadPagos)
                        ->for($deudor)
                        ->create();
                }
            });
    }
}
