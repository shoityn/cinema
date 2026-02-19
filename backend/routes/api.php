<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\TmdbController;
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
Route::prefix('movies')->group(function () {
    Route::get('/', [MovieController::class, 'index']);
    Route::get('/{movie}', [MovieController::class, 'show']); // colocar mais detalhes para puxar no método
    Route::post('/', [MovieController::class, 'store']);   //retorna 200 ok mas não envia
    Route::put('/{movie}', [MovieController::class, 'update']);//
    Route::patch('/{movie}/publish', [MovieController::class, 'publish']);// retornar msg de sucesso
    Route::patch('/{movie}/archive', [MovieController::class, 'archive']);// retornar mensagem de sucesso
});

Route::get('/tmdb/now-playing', [TmdbController::class, 'nowPlaying']);


Route::prefix('tmdb')->group(function () {
    Route::get('/search', [TmdbController::class, 'search']); //
    Route::get('/movies/{tmdbId}', [TmdbController::class, 'details']);
});
    
    
    // Route::get('/tmdb/search', [TmdbController::class, 'search']);
    
    // Detalhes completos (para preencher form)
    // Route::get('/movies/{tmdbId}', [TmdbController::class, 'details']);

