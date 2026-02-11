<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TmdbService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.tmdb.url');
        $this->apiKey  = config('services.tmdb.key');
    }


    public function nowPlaying(int $limit = 5): array
    {
        return Cache::remember('tmdb_now_playing', 600, function () use ($limit) {

            $response = Http::get("{$this->baseUrl}/movie/now_playing", [
                'api_key' => $this->apiKey,
                'language' => 'pt-BR',
                'page' => 1
            ]);

            if (!$response->successful()) {
                throw new \Exception('Erro TMDB: ' . $response->body());
            }

            $data = $response->json();

            if (!isset($data['results'])) {
                throw new \Exception('Resposta inválida do TMDB');
            }

            $movies = array_slice($data['results'], 0, $limit);

            return collect($movies)->map(function ($movie) {

                return [
                    'tmdb_id'      => $movie['id'] ?? null,
                    'titulo'       => $movie['title'] ?? null,
                    'sinopse'      => $movie['overview'] ?? null,
                    'poster_url'   => isset($movie['poster_path'])
                        ? "https://image.tmdb.org/t/p/w500{$movie['poster_path']}"
                        : null,
                    'backdrop_url' => isset($movie['backdrop_path'])
                        ? "https://image.tmdb.org/t/p/w780{$movie['backdrop_path']}"
                        : null,
                    'data_lancamento' => $movie['release_date'] ?? null,
                ];
            })->toArray();
        });
    }

    public function searchMovies(string $query)
    {
        $response = Http::get("https://api.themoviedb.org/3/search/movie", [
            'api_key' => $this->apiKey,
            'query' => $query,
            'language' => 'pt-BR'
        ]);

        return $response->json()['results'] ?? [];
    }


    public function search(string $query, int $limit = 5): array
    {
        if (empty($query)) {
            return [];
        }

        $response = Http::get("{$this->baseUrl}/search/movie", [
            'api_key'  => $this->apiKey,
            'language' => 'pt-BR',
            'query'    => $query,
            'page'     => 1,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Erro TMDB Search: ' . $response->body());
        }

        $data = $response->json();

        if (!isset($data['results'])) {
            return [];
        }

        $movies = array_slice($data['results'], 0, $limit);

        return collect($movies)->map(function ($movie) {

            return [
                'tmdb_id' => $movie['id'] ?? null,
                'titulo'  => $movie['title'] ?? null,
                'sinopse' => $movie['overview'] ?? null,
                'poster_url' => isset($movie['poster_path'])
                    ? "https://image.tmdb.org/t/p/w500{$movie['poster_path']}"
                    : null,
                'backdrop_url' => isset($movie['backdrop_path'])
                    ? "https://image.tmdb.org/t/p/w780{$movie['backdrop_path']}"
                    : null,
                'data_lancamento' => $movie['release_date'] ?? null,
            ];
        })->toArray();
    }
}
