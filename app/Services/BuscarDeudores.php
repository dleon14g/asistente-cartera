<?php

namespace App\Services;

use App\Models\Deudor;
use Illuminate\Support\Collection;

class BuscarDeudores
{
    /**
     * Busca deudores según filtros opcionales.
     *
     * @param  array{provincia?: ?string, dias_mora_minimo?: ?string, estado?: ?string}  $filtros
     */
    public function ejecutar(array $filtros): Collection
    {
        $provincia = $filtros['provincia'] ?? null;
        $diasMoraMinimo = $filtros['dias_mora_minimo'] ?? null;
        $estado = $filtros['estado'] ?? null;

        return Deudor::query()
            ->select(['id', 'nombre', 'dni', 'provincia', 'deuda', 'dias_mora', 'estado'])
            ->when(filled($provincia), fn ($query) => $query->where('provincia', 'like', "%{$provincia}%"))
            ->when(filled($diasMoraMinimo), fn ($query) => $query->where('dias_mora', '>=', $diasMoraMinimo))
            ->when(filled($estado), fn ($query) => $query->where('estado', $estado))
            ->limit(10)
            ->get();
    }
}
