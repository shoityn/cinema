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
        Schema::create('media', function (Blueprint $table) {
            $table->id('media_id');

            $table->foreignId('movie_id')->constrained('movies', 'movie_id')->cascadeOnDelete();

            $table->enum('type', ['poster', 'backdrop', 'logo', 'trailer']);
            $table->enum('provider', ['tmdb', 'local']);

            $table->string('path')->nullable();
            $table->string('external_key')->nullable();

            $table->json('metadata')->nullable();

            $table->boolean('is_primary')->default(false);

            $table->timestamps();

            $table->index(['movie_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
