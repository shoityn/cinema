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
            // Files are saved under public/imgs and stored as "imgs/filename.ext".
            // Return a public URL under the webroot: /imgs/filename.ext
            $path = (string) $media->path;
            $path = ltrim($path, '/');

            // If path was accidentally stored with a storage/ prefix, strip it.
            if (strpos($path, 'storage/backend/public/') === 0) {
                $path = substr($path, strlen('storage/backend/public/'));
            }

            // if (strpos($path, 'storage/backend/public') === 0) {
            //     $path = substr($path, strlen('storage/backend/public'));
            
            // }

            return asset($path);
        }

        return null;
    }
}
