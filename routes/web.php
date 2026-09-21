<?php

use Illuminate\Support\Facades\Route;
use App\Services\ClaudeClient;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/prueba-claude', function (ClaudeClient $claude) {
    $response = $claude->enviar([
        ['role' => 'user', 'content' => 'Explicame en dos oraciones qué es una cartera de cobranzas.'],
    ]);

    return $response->json();
});