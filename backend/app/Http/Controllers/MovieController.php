<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Http\Resources\MovieResource;
use App\Models\Movie;
use App\Services\MovieService;
use Illuminate\Http\JsonResponse;
use DomainException;
use Throwable;

class MovieController extends Controller
{
    public function __construct(
        private MovieService $movieService
    ) {}

    public function index(): JsonResponse
    {
        $movies = Movie::query()
            ->where('status', 'published')
            ->with(['genres', 'media'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json([
            'data' => MovieResource::collection($movies),
            'meta' => [
                'current_page' => $movies->currentPage(),
                'last_page' => $movies->lastPage(),
                'per_page' => $movies->perPage(),
                'total' => $movies->total(),
            ]
        ]);
    }

    public function store(StoreMovieRequest $request): JsonResponse
    {

        try {
            $movie = $this->movieService->create($request->validated());

            return response()->json($movie, 201);

            return response()->json([
                'message' => 'Filmes Criado com Sucesso',
                'data' => new MovieResource($movie->load(['genres', 'media'])),
            ], 201);

        } catch (Throwable $e) {

            return response()->json([
                'message' => 'Erro ao Criar o Filme',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Movie $movie): JsonResponse
    {
        if ($movie->status !== 'published') {
            return response()->json([
                'message' => 'Filmes não encontrado'
            ], 404);
        }

        return response()->json([
            'data' => new MovieResource($movie->load(['genres', 'media'])),
        ]);
    }

    public function update(UpdateMovieRequest $request, Movie $movie): JsonResponse
    {
        try {
            $movie = $this->movieService->update($movie, $request->validated());

            return response()->json([
                'message' => 'Filme atualizado Com Sucesso',
                'data' => new MovieResource($movie->load(['genres', 'media'])),
            ]);

        } catch (Throwable $e) {

            return response()->json([
                'message' => 'Erro ao atualizar o filme',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function archive(Movie $movie): JsonResponse
    {
        try {
            $movie = $this->movieService->archive($movie);

            return response()->json([
                'message' => 'filme arquivado com sucesso',
                'data' => new MovieResource($movie->load(['genres', 'media'])),
            ]);

        } catch (DomainException $e) {

            return response()->json([
                'message' => $e->getMessage(),
            ], 409);

        } catch (Throwable $e) {

            return response()->json([
                'message' => 'Erro ao Arquivar o filme',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function publish(Movie $movie): JsonResponse
    {
        try {
            $movie = $this->movieService->publish($movie);

            return response()->json([
                'message' => 'Filme publicado com Sucesso',
                'data' => new MovieResource($movie->load(['genres', 'media'])),
            ]);

        } catch (DomainException $e) {

            return response()->json([
                'message' => $e->getMessage(),
            ], 409);

        } catch (Throwable $e) {

            return response()->json([
                'message' => 'Erro ao publicar o filme',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}