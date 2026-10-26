<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ClaudeClient
{
    public function enviar(array $mensajes): Response
    {
        return Http::withHeaders([
            'x-api-key' => config('services.anthropic.key'),
            'anthropic-version' => '2023-06-01',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => config('services.anthropic.model'),
            'max_tokens' => 2000,
            'messages' => $mensajes,
            'tools' => $this->herramientas(),
        ]);
    }

    //devuelve un array con las herramientas disponibles para el modelo Claude
    private function herramientas(): array    
    {
        $herramientas = [
            [
                'name' => 'buscar_deudores',
                'description' => 'Busca deudores en la base de datos de una cartera de cobranzas con su deuda y con su mora, el resultado esta limitado a una cantidad maxima de registros.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'provincia' => ['type' => 'string', 'description' => 'la provincia del deudor, corresponde al nombre completo de una provincia argentina', ],
                        'dias_mora_minimo' => ['type' => 'integer', 'description' => 'los dias de mora del deudor, los deudores que superen el mínimo deben ser informados.'],
                        'estado' => [
                            'type' => 'string',
                            'enum' => ['al_dia', 'en_mora', 'en_gestion', 'incobrable'],
                            'description' => 'el estado de la cuenta, no lo infieras a partir de los dias de mora, el estado es un campo que se encuentra en la base de datos.',
                        ],
                    ],
                ],
            ],
        ];

        return $herramientas;
    }
}


