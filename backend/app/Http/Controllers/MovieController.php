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

    /**
     * Move files from a PHP $_FILES entry (which can be nested) into public/imgs.
     * Returns array of relative paths moved (e.g. imgs/filename.jpg).
     */
    private function moveUploadedFilesToImgs(array $fileEntry): array
    {
        $moved = [];

        // If this entry is a nested structure (arrays of name/tmp_name/...)
        if (is_array($fileEntry['tmp_name'] ?? null)) {
            foreach ($fileEntry['tmp_name'] as $k => $subTmp) {
                $sub = [
                    'name' => $fileEntry['name'][$k] ?? null,
                    'type' => $fileEntry['type'][$k] ?? null,
                    'tmp_name' => $fileEntry['tmp_name'][$k] ?? null,
                    'error' => $fileEntry['error'][$k] ?? null,
                    'size' => $fileEntry['size'][$k] ?? null,
                ];
                $moved = array_merge($moved, $this->moveUploadedFilesToImgs($sub));
            }

            return $moved;
        }

        // Leaf entry
        $tmp = $fileEntry['tmp_name'] ?? null;
        $name = $fileEntry['name'] ?? null;
        if ($tmp && is_uploaded_file($tmp) && $name) {
            $dir = base_path('public' . DIRECTORY_SEPARATOR . 'imgs');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            $filename = time() . '_' . uniqid() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $name);
            if (move_uploaded_file($tmp, $dir . DIRECTORY_SEPARATOR . $filename)) {
                $moved[] = 'imgs/' . $filename;
            }
        }

        return $moved;
    }

    public function index(Request $request)
    {
        // Allow the client to request all movies (no pagination) with `?all=1`.
        if ($request->query('all')) {
            $movies = Movie::with(['genres', 'media'])->get();
            return MovieResource::collection($movies);
        }

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

        // If genres were sent as JSON string (FormData), decode
        if (isset($data['genres']) && is_string($data['genres'])) {
            $decoded = json_decode($data['genres'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['genres'] = $decoded;
            }
        }

        // Handle uploaded media files (support different ways PHP may parse FormData)
        $sections = ['poster', 'backdrop', 'logo', 'trailer'];
        $allFiles = $request->allFiles();

        foreach ($sections as $s) {
            $file = null;

            // Prefer structured files array: media => [ poster => [ 'file' => UploadedFile ] ]
            if (isset($allFiles['media']) && is_array($allFiles['media']) && isset($allFiles['media'][$s])) {
                $candidate = $allFiles['media'][$s];
                if (is_array($candidate) && isset($candidate['file'])) {
                    $file = $candidate['file'];
                } elseif ($candidate instanceof \Illuminate\Http\UploadedFile) {
                    $file = $candidate;
                }
            }

            // Fallback to dot notation
            if (!$file && $request->hasFile("media.$s.file")) {
                $file = $request->file("media.$s.file");
            }

            // Another fallback: direct file under media[$s]
            if (!$file && $request->hasFile("media.$s")) {
                $file = $request->file("media.$s");
            }

            if ($file) {
                $dir = base_path('public' . DIRECTORY_SEPARATOR . 'imgs');
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }

                $filename = time() . '_' . uniqid() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
                $file->move($dir, $filename);
                $data['media'][$s]['provider'] = 'local';
                $data['media'][$s]['path'] = 'imgs/' . $filename;
            }
        }

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

        // If genres were sent as JSON string (FormData), decode
        if (isset($data['genres']) && is_string($data['genres'])) {
            $decoded = json_decode($data['genres'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['genres'] = $decoded;
            }
        }

        // Handle uploaded media files
        $sections = ['poster', 'backdrop', 'logo', 'trailer'];
        $allFiles = $request->allFiles();

        // If Laravel didn't parse files but PHP $_FILES has entries, move them (simple fallback)
        if (empty($allFiles) && !empty($_FILES)) {
            foreach ($_FILES as $topKey => $fileEntry) {
                $moved = $this->moveUploadedFilesToImgs($fileEntry);
                // if moved, optionally map first moved file into media.poster.path when ambiguous
                if (!empty($moved) && empty($data['media'])) {
                    $data['media']['poster'] = ['provider' => 'local', 'path' => $moved[0]];
                }
            }
        }

        foreach ($sections as $s) {
            $file = null;

            if (isset($allFiles['media']) && is_array($allFiles['media']) && isset($allFiles['media'][$s])) {
                $candidate = $allFiles['media'][$s];
                if (is_array($candidate) && isset($candidate['file'])) {
                    $file = $candidate['file'];
                } elseif ($candidate instanceof \Illuminate\Http\UploadedFile) {
                    $file = $candidate;
                }
            }

            if (!$file && $request->hasFile("media.$s.file")) {
                $file = $request->file("media.$s.file");
            }

            if (!$file && $request->hasFile("media.$s")) {
                $file = $request->file("media.$s");
            }

            if ($file) {
                $dir = base_path('public' . DIRECTORY_SEPARATOR . 'imgs');
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }

                $filename = time() . '_' . uniqid() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
                $file->move($dir, $filename);
                $data['media'][$s]['provider'] = 'local';
                $data['media'][$s]['path'] = 'imgs/' . $filename;
            }
        }

        // Normalize incoming genres into an array of local genre IDs.
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

        if (!empty($genreIds)) {
            $data['genres'] = $genreIds;
        }

        try {
            $movie = $this->service->update($movie, $data);

            return response()->json([
                'message' => 'Filme atualizado com sucesso',
                'data' => new MovieResource($movie),
            ]);
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
