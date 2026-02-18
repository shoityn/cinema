<?php

namespace App\Services;

use App\Models\Movie;
use Illuminate\Support\Facades\DB;
use DomainException;
use App\Services\TmdbService;
use App\Models\Media;
use App\Models\Genre;
use Carbon\Carbon;

class MovieService
{
    public function create(array $data)
    {

        return DB::transaction(function () use ($data): Movie {

            if (empty($data['status'])) {
                $data['status'] = 'draft';
            }

            return Movie::create($data);
        });
    }

    /**
     * Import a movie from TMDB by its tmdb id.
     */
    public function importFromTmdb(int $tmdbId): Movie
    {
        $tmdb = app(TmdbService::class);

        if (Movie::where('tmdb_id', $tmdbId)->exists()) {
            throw new DomainException('Movie with tmdb_id already exists.');
        }

        $data = $tmdb->getMovieDetails($tmdbId);

        return DB::transaction(function () use ($data) {

            $movie = Movie::create([
                'title' => $data['title'] ?? null,
                'overview' => $data['overview'] ?? null,
                'release_date' => isset($data['release_date']) && !empty($data['release_date']) ? Carbon::parse($data['release_date']) : null,
                'duration_minutes' => $data['runtime'] ?? null,
                'status' => 'draft',
                'tmdb_id' => $data['id'] ?? null,
                'imdb_id' => $data['imdb_id'] ?? null,
                'homepage' => $data['homepage'] ?? null,
            ]);

            // handle images
            $images = $data['images'] ?? [];

            // posters
            if (!empty($images['posters'])) {
                $first = $images['posters'][0];
                Media::create([
                    'movie_id' => $movie->movie_id,
                    'type' => 'poster',
                    'provider' => 'tmdb',
                    'path' => $first['file_path'] ?? null,
                    'metadata' => $first,
                    'is_primary' => true,
                ]);
            }

            // backdrops
            if (!empty($images['backdrops'])) {
                $first = $images['backdrops'][0];
                Media::create([
                    'movie_id' => $movie->movie_id,
                    'type' => 'backdrop',
                    'provider' => 'tmdb',
                    'path' => $first['file_path'] ?? null,
                    'metadata' => $first,
                    'is_primary' => true,
                ]);
            }

            // logos
            if (!empty($images['logos'])) {
                $first = $images['logos'][0];
                Media::create([
                    'movie_id' => $movie->movie_id,
                    'type' => 'logo',
                    'provider' => 'tmdb',
                    'path' => $first['file_path'] ?? null,
                    'metadata' => $first,
                    'is_primary' => true,
                ]);
            }

            // videos (trailers)
            $videos = $data['videos']['results'] ?? [];
            $trailer = null;
            foreach ($videos as $v) {
                if (($v['type'] ?? '') === 'Trailer' && strtolower($v['site'] ?? '') === 'youtube') {
                    $trailer = $v;
                    break;
                }
            }

            if ($trailer) {
                Media::create([
                    'movie_id' => $movie->movie_id,
                    'type' => 'trailer',
                    'provider' => 'tmdb',
                    'external_key' => $trailer['key'] ?? null,
                    'metadata' => $trailer,
                    'is_primary' => true,
                ]);
            }

            // associate genres by tmdb_id -> local genre ids
            $genreIds = [];
            if (!empty($data['genres'])) {
                foreach ($data['genres'] as $g) {
                    if (isset($g['id'])) {
                        $local = Genre::where('tmdb_id', $g['id'])->first();
                        if ($local) {
                            $genreIds[] = $local->genre_id;
                        }
                    }
                }
            }

            if (!empty($genreIds)) {
                $movie->genres()->sync($genreIds);
            }

            return $movie->fresh();
        });
    }

    public function update(Movie $movie, array $data): Movie
    {
        return DB::transaction(function () use ($movie, $data) {

            $movie->update($data);

            return $movie->fresh();
        });
    }

    public function publish(Movie $movie): Movie
    {
        if ($movie->status === 'published') {
            throw new DomainException('Filme já está publicado.');
        }

        return DB::transaction(function () use ($movie) {

            $movie->update(['status' => 'published']);

            return $movie->fresh();
        });
    }

    public function archive(Movie $movie): Movie
    {
        if ($movie->status === 'archived') {
            throw new DomainException('Filme já está arquivado.');
        }

        return DB::transaction(function () use ($movie) {

            $movie->update(['status' => 'archived']);

            return $movie->fresh();
        });
    }

    /**
     * Create a movie with media and genres from a validated payload.
     * This method orchestrates persistence but intentionally leaves
     * strategic business rules as TODOs so the caller/team can implement them.
     */
    public function createWithMedia(array $data): Movie
    {
        return DB::transaction(function () use ($data) {

            // TODO: Business rules to consider before creating:
            // - duplicate detection by tmdb_id (decide whether to update or fail)
            // - validation of provider-specific requirements (e.g. local file handling)
            // - selection/normalization of primary media when multiple items arrive
            // - any permission/owner attribution rules

            $movie = Movie::create([
                'title' => $data['title'] ?? null,
                'overview' => $data['overview'] ?? null,
                'release_date' => $data['release_date'] ?? null,
                'duration_minutes' => $data['duration_minutes'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'tmdb_id' => $data['tmdb_id'] ?? null,
                'imdb_id' => $data['imdb_id'] ?? null,
                'homepage' => $data['homepage'] ?? null,
            ]);

            // media handling
            $mediaPayload = $data['media'] ?? [];

            $types = ['poster', 'backdrop', 'logo', 'trailer'];

            foreach ($types as $type) {
                if (empty($mediaPayload[$type]) || !is_array($mediaPayload[$type])) {
                    continue;
                }

                $m = $mediaPayload[$type];

                $mediaData = [
                    'movie_id' => $movie->movie_id,
                    'type' => $type,
                    'provider' => $m['provider'] ?? 'tmdb',
                    'path' => $m['path'] ?? null,
                    'external_key' => $m['external_key'] ?? null,
                    'metadata' => $m['metadata'] ?? null,
                    'is_primary' => true, // default incoming single items as primary
                ];

                Media::create($mediaData);
            }

            // genres sync (expects array of local genre ids)
            if (!empty($data['genres']) && is_array($data['genres'])) {
                $movie->genres()->sync($data['genres']);
            }

            return $movie->fresh()->load(['genres', 'media']);
        });
    }
}