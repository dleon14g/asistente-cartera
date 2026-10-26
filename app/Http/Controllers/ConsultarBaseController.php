<?php

namespace App\Http\Controllers;

use App\Services\BuscarDeudores;
use Illuminate\Http\Request;

class ConsultarBaseController extends Controller
{
    public function __construct(
        private readonly BuscarDeudores $buscarDeudores,
    ) {
    }

    public function consultarBase(Request $request)
    {
        return $this->buscarDeudores->ejecutar([
            'provincia' => $request->query('provincia'),
            'dias_mora_minimo' => $request->query('moraMinima'),
            'estado' => $request->query('estado'),
        ]);
    }
}
