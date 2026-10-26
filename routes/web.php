<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Services\ClaudeClient;
use App\Services\AgenteCartera;
use App\Http\Controllers\ConsultarBaseController;

Route::get('/', function () {return view('welcome');});

Route::get('/consultar-base', [App\Http\Controllers\ConsultarBaseController::class, 'consultarBase'])->name('consultar-base');

Route::get('/consultar-base/formulario', function () {return view('consultar-base');});

Route::get('/preguntar', function (Request $request, AgenteCartera $agente) {
    $pregunta = $request->query('pregunta');

    if (! filled($pregunta)) {
        return response('Falta el parámetro "pregunta".', 422);
    }

    return $agente->responder($pregunta);})->name('preguntar');