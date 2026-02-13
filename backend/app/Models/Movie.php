<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $primaryKey = 'movie_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'title',
        'overview',
        'release_date',
        'duration_minutes',
        'status',
        'tmdb_id',
        'imdb_id',
        'homepage',
    ];

    protected $casts = [
        'release_date' => 'date',
    ];

    public function media()
    {
        return $this->hasMany(Media::class, 'movie_id', 'movie_id');
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'genre_movie', 'movie_id', 'genre_id')
                    ->withTimestamps();
    }

    public function primaryMedia($type)
    {
        return $this->media()
                    ->where('type', $type)
                    ->where('is_primary', true)
                    ->first();
    }
}