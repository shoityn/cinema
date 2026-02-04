<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Filme extends Model
{
    protected $table = 'filmes';

    /**
     * Campos que podem ser preenchidos via mass assignment
     */
    protected $fillable = [
        'titulo',
        'sinopse',
        'data_lancamento',
        'duracao_minutos',
        'trailer_url',
        'poster_url',
        'backdrop_url',
        'status',
        'tmdb_id',
    ];

    /**
     * Casts de atributos
     */
    protected $casts = [
        'data_lancamento' => 'date',
    ];

    /**
     * Impede alteração do tmdb_id após criação
     */
    protected static function booted()
    {
        static::updating(function ($filme) {
            if ($filme->isDirty('tmdb_id')) {
                throw new \RuntimeException('tmdb_id não pode ser alterado após a criação do filme.');
            }
        });
    }


    public function generos()
    {
        return $this->belongsToMany(Genero::class, 'filme_genero');
    }
}
