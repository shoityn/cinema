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

    return [
        'movie_id' => $this->movie_id,
        'title' => $this->title,

        'genres' => GenreResource::collection($this->whenLoaded('genres')),

        'media' => [
            'poster_url' => $resolver->resolve($this->primaryMedia('poster')),
            'backdrop_url' => $resolver->resolve($this->primaryMedia('backdrop')),
            'logo_url' => $resolver->resolve($this->primaryMedia('logo')),
            'trailer_url' => $resolver->resolve($this->primaryMedia('trailer')),
        ],
    ];
}
}