<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FilmeController;
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

Route::prefix('filmes')->group(function () {
    Route::get('/', [FilmeController::class, 'index']);      // LISTAGEM
    Route::get('/{filme}', [FilmeController::class, 'show']); // DETALHE

    Route::post('/', [FilmeController::class, 'store']);     // CREATE
    Route::put('/{filme}', [FilmeController::class, 'update']); // UPDATE
    Route::delete('/{filme}', [FilmeController::class, 'destroy']); //DELETE(inativa)
});
