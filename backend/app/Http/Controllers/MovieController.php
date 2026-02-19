<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Movie;
use App\Http\Resources\MovieResource;
use App\Http\Requests\StoreMovieRequest;
use App\Services\MovieService;
use App\Models\Genre;
use Illuminate\Http\JsonResponse;
use DomainException;
use Throwable;

class MovieController extends Controller
{
    protected MovieService $service;

    public function __construct(MovieService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $movies = Movie::with(['genres', 'media'])->paginate(15);

        return MovieResource::collection($movies);
    }

    public function show(Movie $movie)
    {
        $movie->load(['genres', 'media']);

        return new MovieResource($movie);
    }

    public function store(StoreMovieRequest $request)
    {
        $data = $request->all();

        // Normalize incoming genres into an array of local genre IDs.
        // Accepts either: [1,2,3] or [{"tmdb_id": 18, ...}, ...]
        $incomingGenres = $data['genres'] ?? [];
        $genreIds = [];

        foreach ($incomingGenres as $g) {
            if (is_int($g)) {
                $genreIds[] = $g;
                continue;
            }

            if (is_array($g) && isset($g['tmdb_id'])) {
                $local = Genre::where('tmdb_id', $g['tmdb_id'])->first();
                if ($local) {
                    $genreIds[] = $local->genre_id;
                }
            }
        }

        // if (empty($genreIds)) {
        //     return response()->json([
        //         'message' => 'Nenhum gênero válido foi fornecido. Envie ids locais ou objetos com tmdb_id que já existam localmente.'
        //     ], 422);
        // }

        $data['genres'] = $genreIds;

        // TODO: Implementar regras de negócio
        // - validar duplicidade por tmdb_id
        // - decidir se atualiza ou bloqueia
        // - selecionar mídia principal
        // - tratar provedores 'local' (salvar arquivos em storage quando aplicável)

        try {
            return DB::transaction(function () use ($data) {
                $movie = $this->service->createWithMedia($data);

                return response()->json([
                    'message' => 'Filme criado com sucesso',
                    'input' => $data,
                    'data' => new MovieResource($movie->load(['genres', 'media'])),
                ], 201);
            });

        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erro ao criar o filme',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function update(Request $request, Movie $movie)
    {
        $data = $request->all();

        try {
            $movie = $this->service->update($movie, $data);

            return new MovieResource($movie->load(['genres', 'media']));
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function publish(Movie $movie)
    {
        try {
            $movie = $this->service->publish($movie);

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

    public function archive(Movie $movie)
    {
        try {
            $movie = $this->service->archive($movie);

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
}
