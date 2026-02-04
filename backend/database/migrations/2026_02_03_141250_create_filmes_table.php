<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('filmes', function (Blueprint $table) {
    $table->id();

    // Identidade externa (TMDB)
    $table->unsignedInteger('tmdb_id')->unique();

    // Dados principais
    $table->string('titulo');
    $table->text('sinopse');
    $table->date('data_lancamento')->nullable();
    $table->integer('duracao_minutos')->nullable();

    // Assets
    $table->string('poster_path')->nullable();
    $table->string('backdrop_path')->nullable();

    // Métricas (home / ordenação)
    $table->decimal('popularidade', 8, 4)->nullable();
    $table->decimal('nota_media', 3, 1)->nullable();
    $table->integer('votos')->nullable();

    // Controle de sincronização
    $table->boolean('detalhes_completos')->default(false);

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filmes');
    }
};
