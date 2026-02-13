<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $primaryKey = 'genre_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'tmdb_id',
        'name',
        'slug',
        'is_official',
    ];

    protected $casts = [
        'is_official' => 'boolean',
    ];

    public function movies()
    {
        return $this->belongsToMany(
            Movie::class,
            'genre_movie',
            'genre_id',
            'movie_id'
        )->withTimestamps();
    }
}