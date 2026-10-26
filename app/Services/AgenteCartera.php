<?php

namespace App\Services;

class AgenteCartera
{
    private const MAX_VUELTAS = 5;

    public function __construct(
        private readonly ClaudeClient $claudeClient,
        private readonly BuscarDeudores $buscarDeudores,
    ) {
    }

    public function responder(string $pregunta): string
    {
        $mensajes = [
            ['role' => 'user', 'content' => $pregunta],
        ];

        $vueltas = 0;

        while ($vueltas < self::MAX_VUELTAS) {
            $vueltas++;

            $respuesta = $this->claudeClient->enviar($mensajes)->json();

            if (($respuesta['stop_reason'] ?? null) !== 'tool_use') {
                return $this->extraerTexto($respuesta['content'] ?? []);
            }

            $mensajes[] = [
                'role' => 'assistant',
                'content' => $respuesta['content'],
            ];

            $resultados = [];

            foreach ($respuesta['content'] as $bloque) {
                if (($bloque['type'] ?? null) !== 'tool_use') {
                    continue;
                }

                $datos = $this->ejecutarHerramienta($bloque['name'], $bloque['input'] ?? []);

                $resultados[] = [
                    'type' => 'tool_result',
                    'tool_use_id' => $bloque['id'],
                    'content' => json_encode($datos),
                ];
            }

            $mensajes[] = [
                'role' => 'user',
                'content' => $resultados,
                
            ];            
        }

        return 'No se pudo obtener una respuesta: se alcanzó el máximo de vueltas permitidas.';
    }

    /**
     * Devuelve el texto plano de los bloques de contenido de una respuesta final.
     */
    private function extraerTexto(array $bloques): string
    {
        return collect($bloques)
            ->where('type', 'text')
            ->pluck('text')
            ->implode("\n");
    }

    /**
     * Despacha la ejecución de una herramienta pedida por el modelo según su nombre.
     */
    private function ejecutarHerramienta(string $nombre, array $input): mixed
    {
        return match ($nombre) {
            'buscar_deudores' => $this->buscarDeudores->ejecutar($input)->toArray(),
            default => ['error' => "Herramienta desconocida: {$nombre}"],
        };
    }
}
