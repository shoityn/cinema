<?php

namespace App\Services;

use App\Models\Movie;
use Illuminate\Support\Facades\DB;
use DomainException;

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
}