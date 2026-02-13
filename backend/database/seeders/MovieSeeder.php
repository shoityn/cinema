<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Movie;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Filme 1: Ativo (Published)
        Movie::create([
            'title' => 'Inception',
            'overview' => 'Dom Cobb é um ladrão com a rara habilidade de entrar nos sonhos das pessoas.',
            'release_date' => '2010-07-16',
            'duration_minutes' => 148,
            'status' => 'published',
            'tmdb_id' => 27205,
            'imdb_id' => 'tt1375666',
            'homepage' => 'https://www.warnerbros.com/movies/inception',
        ]);

        // Filme 2: Ativo (Published)
        Movie::create([
            'title' => 'The Matrix',
            'overview' => 'Um programador descobre que a realidade é uma simulação criada por máquinas.',
            'release_date' => '1999-03-31',
            'duration_minutes' => 136,
            'status' => 'published',
            'tmdb_id' => 603,
            'imdb_id' => 'tt0133093',
            'homepage' => 'https://www.warnerbros.com/movies/matrix',
        ]);

        // Filme 3: Inativo (Archived ou Draft)
        Movie::create([
            'title' => 'Filme de Teste Antigo',
            'overview' => 'Este filme está arquivado e não deve aparecer na listagem principal.',
            'release_date' => '2020-01-01',
            'duration_minutes' => 90,
            'status' => 'archived',
            'tmdb_id' => 999999, // ID fictício
            'imdb_id' => null,
            'homepage' => null,
        ]);
    }
}