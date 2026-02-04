<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('filmes', function (Blueprint $table) {
            $table->id();

            // Identidade externa (opcional, única)
            $table->unsignedInteger('tmdb_id')->nullable()->unique();

            // Dados centrais
            $table->string('titulo', 255);
            $table->text('sinopse')->nullable();

            // Informações temporais
            $table->date('data_lancamento')->nullable();
            $table->unsignedSmallInteger('duracao_minutos')->nullable();

            // Mídias
            $table->string('trailer_url', 500)->nullable();
            $table->string('poster_url', 255)->nullable();
            $table->string('backdrop_url', 255)->nullable();

            // Estado
            $table->enum('status', ['ativo', 'inativo'])->default('ativo');

            // Timestamps padrão Laravel
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filmes');
    }
};