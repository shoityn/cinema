<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\MediaUrlResolver;

class GenreResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->getKey(),
            'tmdb_id' => $this->tmdb_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'is_official' => (bool) $this->is_official,
        ];
    }
}