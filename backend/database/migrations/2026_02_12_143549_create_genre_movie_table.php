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
        Schema::create('genre_movie', function (Blueprint $table) {

            $table->unsignedBigInteger('movie_id');
            $table->unsignedBigInteger('genre_id');

            $table->foreign('movie_id')
                ->references('movie_id')
                ->on('movies')
                ->cascadeOnDelete();

            $table->foreign('genre_id')
                ->references('genre_id')
                ->on('genres')
                ->cascadeOnDelete();

            $table->primary(['movie_id', 'genre_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genre_movie');
    }
};
