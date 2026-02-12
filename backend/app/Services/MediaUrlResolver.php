<?php

namespace App\Services;

use App\Models\Media;

class MediaUrlResolver
{
    public function resolve(?Media $media): ?string
    {
        if (!$media) {
            return null;
        }

        if ($media->provider === 'tmdb') {

            if ($media->type === 'trailer') {
                return "https://www.youtube.com/watch?v={$media->external_key}";
            }

            return "https://image.tmdb.org/t/p/original{$media->path}";
        }

        if ($media->provider === 'local') {
            return asset("storage/{$media->path}");
        }

        return null;
    }
}