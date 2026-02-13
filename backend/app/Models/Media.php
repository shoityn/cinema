<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $primaryKey = 'media_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'movie_id',
        'type',
        'provider',
        'path',
        'external_key',
        'metadata',
        'is_primary',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_primary' => 'boolean',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class, 'movie_id', 'movie_id');
    }
}