<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\MediaUrlResolver;

class MovieResource extends JsonResource
{
    public function toArray($request)
{
    $resolver = app(MediaUrlResolver::class);
    $poster = $this->primaryMedia('poster');
    $backdrop = $this->primaryMedia('backdrop');
    $logo = $this->primaryMedia('logo');
    $trailer = $this->primaryMedia('trailer');

    return [
        'movie_id' => $this->movie_id,
        'title' => $this->title,
        'overview' => $this->overview,
        'release_date' => $this->release_date ? $this->release_date->toDateString() : null,
        'duration_minutes' => $this->duration_minutes,
        'tmdb_id' => $this->tmdb_id,
        'imdb_id' => $this->imdb_id,
        'homepage' => $this->homepage,

        'genres' => GenreResource::collection($this->whenLoaded('genres')),

        'media' => [
            'poster' => $poster ? [
                'provider' => $poster->provider,
                'path' => $poster->path,
                'url' => $resolver->resolve($poster),
            ] : null,
            'backdrop' => $backdrop ? [
                'provider' => $backdrop->provider,
                'path' => $backdrop->path,
                'url' => $resolver->resolve($backdrop),
            ] : null,
            'logo' => $logo ? [
                'provider' => $logo->provider,
                'path' => $logo->path,
                'url' => $resolver->resolve($logo),
            ] : null,
            'trailer' => $trailer ? [
                'provider' => $trailer->provider,
                'external_key' => $trailer->external_key,
                'url' => $resolver->resolve($trailer),
            ] : null,
        ],
    ];
}
}