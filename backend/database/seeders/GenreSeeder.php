<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Genre;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            ['tmdb_id' => 28, 'name' => 'Ação', 'slug' => 'action'],
            ['tmdb_id' => 12, 'name' => 'Aventura', 'slug' => 'adventure'],
            ['tmdb_id' => 16, 'name' => 'Animação', 'slug' => 'animation'],
            ['tmdb_id' => 35, 'name' => 'Comédia', 'slug' => 'comedy'],
            ['tmdb_id' => 80, 'name' => 'Crime', 'slug' => 'crime'],
            ['tmdb_id' => 99, 'name' => 'Documentário', 'slug' => 'documentary'],
            ['tmdb_id' => 18, 'name' => 'Drama', 'slug' => 'drama'],
            ['tmdb_id' => 10751, 'name' => 'Família', 'slug' => 'family'],
            ['tmdb_id' => 14, 'name' => 'Fantasia', 'slug' => 'fantasy'],
            ['tmdb_id' => 36, 'name' => 'História', 'slug' => 'history'],
            ['tmdb_id' => 27, 'name' => 'Terror', 'slug' => 'horror'],
            ['tmdb_id' => 10402, 'name' => 'Música', 'slug' => 'music'],
            ['tmdb_id' => 9648, 'name' => 'Mistério', 'slug' => 'mystery'],
            ['tmdb_id' => 10749, 'name' => 'Romance', 'slug' => 'romance'],
            ['tmdb_id' => 878, 'name' => 'Ficção Científica', 'slug' => 'science-fiction'],
            ['tmdb_id' => 10770, 'name' => 'Cinema para TV', 'slug' => 'tv-movie'],
            ['tmdb_id' => 53, 'name' => 'Suspense', 'slug' => 'thriller'],
            ['tmdb_id' => 10752, 'name' => 'Guerra', 'slug' => 'war'],
            ['tmdb_id' => 37, 'name' => 'Faroeste', 'slug' => 'western'],
        ];

        foreach ($genres as $genre) {
            Genre::updateOrCreate(
                ['tmdb_id' => $genre['tmdb_id']],
                [
                    'name' => $genre['name'],
                    'slug' => $genre['slug'],
                    'is_official' => true,
                ]
            );
        }
    }
}
