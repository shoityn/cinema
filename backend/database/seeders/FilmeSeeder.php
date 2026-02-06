<?php

namespace Database\Seeders;

use App\Models\Filme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FilmeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //truncate apenas para limpar
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('filmes')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // --- Filmes Ativos ---

        Filme::create([
            'tmdb_id' => 1034541,
            'titulo' => 'Avatar: Fogo e Cinzas',
            'sinopse' => 'Jake Sully e Neytiri enfrentam uma nova ameaça em Pandora: o Povo das Cinzas, um clã de Na\'vi que revela um lado mais sombrio da cultura do planeta.',
            'data_lancamento' => '2025-12-19',
            'duracao_minutos' => 197,
            'trailer_url' => 'https://youtube.com/watch?v=N_v9Vf-9j7Q',
            'poster_url' => '/nzfd4GSmANTyJVVryteFU3Xjuje.jpg',
            'backdrop_url' => '/lu7VARBXX1B5XjwYh3KhcV7WlGW.jpg',
            'status' => 'ativo',
        ]);

        Filme::create([
            'tmdb_id' => 1368166,
            'titulo' => 'A Empregada',
            'sinopse' => 'Millie aceita trabalhar para uma família rica, mas logo descobre que a casa esconde segredos perigosos e que sua patroa é instável.',
            'data_lancamento' => '2025-01-01',
            'duracao_minutos' => 131,
            'trailer_url' => 'https://youtube.com/watch?v=A2Gsh4bE6t4',
            'poster_url' => '/posters/a-empregada.jpg',
            'backdrop_url' => '/backdrops/a-empregada.jpg',
            'status' => 'ativo',
        ]);

        Filme::create([
            'tmdb_id' => 862,
            'titulo' => 'Toy Story',
            'sinopse' => 'O boneco caubói Woody vê seu reinado ameaçado quando o moderno patrulheiro espacial Buzz Lightyear chega ao quarto de Andy.',
            'data_lancamento' => '1995-11-22',
            'duracao_minutos' => 81,
            'trailer_url' => 'https://youtube.com/watch?v=v-PjgYDrg70',
            'poster_url' => '/posters/toy-story.jpg',
            'backdrop_url' => '/backdrops/toy-story.jpg',
            'status' => 'ativo',
        ]);

        Filme::create([
            'tmdb_id' => 1368,
            'titulo' => 'Rambo: Programado para Matar',
            'sinopse' => 'Um veterano da Guerra do Vietnã usa suas habilidades de sobrevivência contra a polícia de uma pequena cidade após sofrer abusos.',
            'data_lancamento' => '1982-10-22',
            'duracao_minutos' => 93,
            'trailer_url' => 'https://youtube.com/watch?v=IAqLKlxY3Yw',
            'poster_url' => '/posters/rambo.jpg',
            'backdrop_url' => '/backdrops/rambo.jpg',
            'status' => 'ativo',
        ]);

        Filme::create([
            'tmdb_id' => 10040,
            'titulo' => 'Robocop: O Policial do Futuro',
            'sinopse' => 'Em uma Detroit futurista, um policial brutalmente assassinado é ressuscitado como um ciborgue imparável para combater o crime.',
            'data_lancamento' => '1987-07-17',
            'duracao_minutos' => 102,
            'trailer_url' => 'https://youtube.com/watch?v=6tC_5mp3udE',
            'poster_url' => '/posters/robocop.jpg',
            'backdrop_url' => '/backdrops/robocop.jpg',
            'status' => 'ativo',
        ]);

        Filme::create([
            'tmdb_id' => 315162,
            'titulo' => 'Gato de Botas 2: O Último Pedido',
            'sinopse' => 'O Gato de Botas descobre que gastou oito de suas nove vidas e parte em uma jornada para encontrar a Estrela dos Desejos.',
            'data_lancamento' => '2022-12-21',
            'duracao_minutos' => 102,
            'trailer_url' => 'https://youtube.com/watch?v=mD9vU3-C5f4',
            'poster_url' => '/posters/gato-de-botas-2.jpg',
            'backdrop_url' => '/backdrops/gato-de-botas-2.jpg',
            'status' => 'ativo',
        ]);

        Filme::create([
            'tmdb_id' => 4935,
            'titulo' => 'O Castelo Animado',
            'sinopse' => 'Uma jovem é amaldiçoada com um corpo de idosa por uma bruxa e busca ajuda em um castelo andante pertencente a um mago.',
            'data_lancamento' => '2004-11-20',
            'duracao_minutos' => 119,
            'trailer_url' => 'https://youtube.com/watch?v=iwROgK94zcM',
            'poster_url' => '/posters/o-castelo-animado.jpg',
            'backdrop_url' => '/backdrops/o-castelo-animado.jpg',
            'status' => 'ativo',
        ]);

        // --- Filmes Inativos ---

        Filme::create([
            'tmdb_id' => 269149,
            'titulo' => 'Zootopia: Essa Cidade é o Bicho',
            'sinopse' => 'Uma coelha policial e uma raposa trapaceira devem trabalhar juntos para desvendar uma conspiração na metrópole de Zootopia.',
            'data_lancamento' => '2016-03-04',
            'duracao_minutos' => 108,
            'trailer_url' => 'https://youtube.com/watch?v=jWM0ct-OLsM',
            'poster_url' => '/posters/zootopia.jpg',
            'backdrop_url' => '/backdrops/zootopia.jpg',
            'status' => 'inativo',
        ]);

        Filme::create([
            'tmdb_id' => 1369,
            'titulo' => 'Rambo 2: A Missão',
            'sinopse' => 'John Rambo é libertado da prisão para uma missão secreta no Vietnã, com o objetivo de localizar prisioneiros de guerra americanos.',
            'data_lancamento' => '1985-05-22',
            'duracao_minutos' => 96,
            'trailer_url' => 'https://youtube.com/watch?v=9mVpxVmbdKY',
            'poster_url' => '/posters/rambo-2.jpg',
            'backdrop_url' => '/backdrops/rambo-2.jpg',
            'status' => 'inativo',
        ]);

        Filme::create([
            'tmdb_id' => 277834,
            'titulo' => 'Moana: Um Mar de Aventuras',
            'sinopse' => 'Uma jovem navegadora parte em uma missão ousada para salvar seu povo, encontrando o semideus Maui no caminho.',
            'data_lancamento' => '2016-11-23',
            'duracao_minutos' => 107,
            'trailer_url' => 'https://youtube.com/watch?v=LKFuXETZUsI',
            'poster_url' => '/posters/moana.jpg',
            'backdrop_url' => '/backdrops/moana.jpg',
            'status' => 'inativo',
        ]);
    }
}
