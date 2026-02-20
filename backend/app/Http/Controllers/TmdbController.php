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

        $results = $this->tmdb->search($q, 5);

            // Normalize to a small payload for frontend selection
            $list = array_map(function ($m) {
                return [
                    'tmdb_id' => isset($m['tmdb_id']) ? (int) $m['tmdb_id'] : (isset($m['id']) ? (int) $m['id'] : null),
                    'title' => $m['titulo'] ?? ($m['title'] ?? null),
                    'overview' => $m['sinopse'] ?? ($m['overview'] ?? null) ?? '',
                    'poster_url' => $m['poster_url'] ?? (isset($m['poster_path']) ? "https://image.tmdb.org/t/p/w500{$m['poster_path']}" : null),
                    'backdrop_url' => $m['backdrop_url'] ?? (isset($m['backdrop_path']) ? "https://image.tmdb.org/t/p/w780{$m['backdrop_path']}" : null),
                    'release_date' => $m['data_lancamento'] ?? ($m['release_date'] ?? null),
                ];
            }, (array) $results);

            // Collect tmdb ids to fetch details
            $ids = array_values(array_filter(array_map(function ($i) { return $i['tmdb_id'] ?? null; }, $list)));

            $detailsMap = [];
            if (!empty($ids)) {
                $detailsMap = $this->tmdb->searchtop($ids); // returns keyed array by id
            }

            // Merge details into the normalized list when available.
            $enriched = array_map(function ($item) use ($detailsMap) {
                $id = $item['tmdb_id'];
                if ($id && isset($detailsMap[$id])) {
                    $d = $detailsMap[$id];

                    // Map TMDB detail fields into our payload shape
                    $item['adult'] = $d['adult'] ?? false;
                    $item['backdrop_path'] = $d['backdrop_path'] ?? null;
                    $item['belongs_to_collection'] = $d['belongs_to_collection'] ?? null;
                    $item['budget'] = $d['budget'] ?? null;
                    $item['genres'] = array_map(function ($g) {
                        return ['id' => $g['id'] ?? null, 'name' => $g['name'] ?? null];
                    }, $d['genres'] ?? []);
                    $item['homepage'] = $d['homepage'] ?? null;
                    $item['id'] = $d['id'] ?? $id;
                    $item['imdb_id'] = $d['imdb_id'] ?? null;
                    $item['original_language'] = $d['original_language'] ?? null;
                    $item['original_title'] = $d['original_title'] ?? null;
                    $item['popularity'] = $d['popularity'] ?? null;
                    $item['poster_path'] = $d['poster_path'] ?? null;
                    $item['production_companies'] = $d['production_companies'] ?? [];
                    $item['production_countries'] = $d['production_countries'] ?? [];
                    $item['release_date'] = $d['release_date'] ?? $item['release_date'];
                    $item['revenue'] = $d['revenue'] ?? null;
                    $item['runtime'] = $d['runtime'] ?? null;
                    $item['spoken_languages'] = $d['spoken_languages'] ?? [];
                    $item['status'] = $d['status'] ?? null;
                    $item['tagline'] = $d['tagline'] ?? null;
                    $item['video'] = $d['video'] ?? false;
                    $item['vote_average'] = $d['vote_average'] ?? null;
                    $item['vote_count'] = $d['vote_count'] ?? null;
                }

                return $item;
            }, $list);

            return response()->json(array_values($enriched));

         $lits2 = $this->tmdb->searchtop($list['tmdb_id']);

        return response()->json(array_values($list, $list2));
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
