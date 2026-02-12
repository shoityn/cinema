<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
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
        return $this->belongsToMany(Movie::class)
                    ->withTimestamps();
    }
}
