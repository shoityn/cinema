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
        Schema::create('movies', function (Blueprint $table) {
            $table->id('movie_id');

            $table->string('title');
            $table->text('overview')->nullable();
            $table->date('release_date')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();

            $table->enum('status', ['draft', 'published', 'archived'])
                ->default('published');

            $table->unsignedBigInteger('tmdb_id')->nullable()->unique();
            $table->string('imdb_id')->nullable();
            $table->string('homepage')->nullable();

            $table->timestamps();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
