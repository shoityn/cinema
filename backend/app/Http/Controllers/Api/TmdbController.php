<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TmdbService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class TmdbController extends Controller
{
    protected $tmdbService;

    public function __construct(TmdbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    public function nowPlaying(TmdbService $tmdb): JsonResponse
    {
        try {
            $movies = $tmdb->nowPlaying(5);

            return response()->json([
                'success' => true,
                'data' => $movies
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar filmes do TMDB',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request)
    {
        $query = $request->query('q');

        if (!$query) {
            return response()->json([]);
        }

        $results = $this->tmdbService->searchMovies($query);

        $limited = collect($results)->take(5)->map(function ($movie) {
            return [
                'id' => $movie['id'],
                'title' => $movie['title'],
                'overview' => $movie['overview'],
                'release_date' => $movie['release_date'],
                'poster_path' => $movie['poster_path'],
            ];
        });

        return response()->json($limited);
    }


    //função para pesquisa de todos 
    // public function search(Request $request, TmdbService $tmdb)
    // {
    //     $query = $request->query('query');

    //     if (!$query) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Parâmetro "query" é obrigatório.'
    //         ], 400);
    //     }

    //     try {
    //         $movies = $tmdb->search($query, 5);

    //         return response()->json([
    //             'success' => true,
    //             'data' => $movies
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Erro ao buscar filmes no TMDB',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
}
