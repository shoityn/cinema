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
        Schema::create('filme_genero', function (Blueprint $table) {
            $table->unsignedBigInteger('filme_id');
            $table->unsignedBigInteger('genero_id');

            $table->primary(['filme_id', 'genero_id']);

            $table->foreign('filme_id')
                ->references('id')->on('filmes')
                ->onDelete('cascade');

            $table->foreign('genero_id')
                ->references('id')->on('generos')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filme_genero');
    }
};
