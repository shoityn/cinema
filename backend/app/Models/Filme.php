<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Filme extends Model
{
    use HasFactory;

    protected $fillable = [
        'tmdb_id',
        'titulo',
        'sinopse',
        'data_lancamento',
        'duracao_minutos',
        'poster_path',
        'backdrop_path',
        'popularidade',
        'nota_media',
        'votos',
        'detalhes_completos',
    ];

    protected $casts = [
        'data_lancamento' => 'date',
        'detalhes_completos' => 'boolean',
    ];

    public function generos()
    {
        return $this->belongsToMany(Genero::class, 'filme_genero');
    }
}
