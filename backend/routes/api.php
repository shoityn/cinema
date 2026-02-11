<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FilmeController;
use App\Http\Controllers\Api\TmdbController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', function (Request $request) {
        return $request->user();
    });
});

// Rotas CRUD para filmes com nomes padrão (filmes.index, filmes.store, ...)
Route::apiResource('filmes', FilmeController::class)
    ->only(['index', 'store', 'show', 'update', 'destroy']);


Route::patch('/filmes/{filme}/ativar', [FilmeController::class, 'activate'])
    ->name('filmes.activate');

Route::get('/tmdb/now-playing', [TmdbController::class, 'nowPlaying']);

Route::get('/tmdb/search', [TmdbController::class, 'search']);