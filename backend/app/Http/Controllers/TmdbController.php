<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TmdbService;

class TmdbController extends Controller
{
    protected TmdbService $tmdb;

    public function __construct(TmdbService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    /**
     * Search TMDB movies.
     * GET /tmdb/search?q=...
     */
    public function search(Request $request)
    {
        $q = $request->query('q', '');

        if (empty($q)) {
            return response()->json([]);
        }

        $results = $this->tmdb->search($q, 20);

        // Normalize to a small payload for frontend selection
        $list = array_map(function ($m) {
            return [
                'tmdb_id' => $m['tmdb_id'] ?? null,
                'title' => $m['titulo'] ?? ($m['title'] ?? null),
                'overview' => $m['sinopse'] ?? null,
                'poster_url' => $m['poster_url'] ?? null,
                'backdrop_url' => $m['backdrop_url'] ?? null,
                'release_date' => $m['data_lancamento'] ?? null,
            ];
        }, $results);

        return response()->json(array_values($list));
    }

    /**
     * Now playing (small list) - exposes TMDB now_playing endpoint
     */
    public function nowPlaying(Request $request)
    {
        $limit = (int) $request->query('limit', 5);

        $data = $this->tmdb->nowPlaying($limit);

        return response()->json($data);
    }

    /**
     * Return movie details from TMDB but do not persist.
     * GET /tmdb/movies/{tmdbId}
     */
    public function details(int $tmdbId)
    {

        $data = $this->tmdb->getMovieDetails($tmdbId);

        // Build a payload ready to fill the frontend form.
        $payload = [
            'title' => $data['title'] ?? null,
            'overview' => $data['overview'] ?? null,
            'release_date' => $data['release_date'] ?? null,
            'duration_minutes' => $data['runtime'] ?? null,
            'tmdb_id' => $data['id'] ?? null,
            'imdb_id' => $data['imdb_id'] ?? null,
            'homepage' => $data['homepage'] ?? null,
            'genres' => [], // tmdb genres (frontend should map to local ids)
            'media' => [
                'poster' => null,
                'backdrop' => null,
                'logo' => null,
                'trailer' => null,
            ],
        ];

        if (!empty($data['genres'])) {
            foreach ($data['genres'] as $g) {
                $payload['genres'][] = [
                    'tmdb_id' => $g['id'] ?? null,
                    'name' => $g['name'] ?? null,
                ];
            }
        }

        // images
        $images = $data['images'] ?? [];
        if (!empty($images['posters'])) {
            $first = $images['posters'][0];
            $payload['media']['poster'] = [
                'provider' => 'tmdb',
                'path' => $first['file_path'] ?? null,
                'url' => isset($first['file_path']) ? "https://image.tmdb.org/t/p/original{$first['file_path']}" : null,
            ];
        }

        if (!empty($images['backdrops'])) {
            $first = $images['backdrops'][0];
            $payload['media']['backdrop'] = [
                'provider' => 'tmdb',
                'path' => $first['file_path'] ?? null,
                'url' => isset($first['file_path']) ? "https://image.tmdb.org/t/p/original{$first['file_path']}" : null,
            ];
        }

        if (!empty($images['logos'])) {
            $first = $images['logos'][0];
            $payload['media']['logo'] = [
                'provider' => 'tmdb',
                'path' => $first['file_path'] ?? null,
                'url' => isset($first['file_path']) ? "https://image.tmdb.org/t/p/original{$first['file_path']}" : null,
            ];
        }

        // videos
        $videos = $data['videos']['results'] ?? [];
        $trailer = null;
        foreach ($videos as $v) {
            if (($v['type'] ?? '') === 'Trailer' && strtolower($v['site'] ?? '') === 'youtube') {
                $trailer = $v;
                break;
            }
        }

        if ($trailer) {
            $payload['media']['trailer'] = [
                'provider' => 'tmdb',
                'external_key' => $trailer['key'] ?? null,
                'url' => isset($trailer['key']) ? "https://www.youtube.com/watch?v={$trailer['key']}" : null,
            ];
        }

        return response()->json($payload);
    }
}
