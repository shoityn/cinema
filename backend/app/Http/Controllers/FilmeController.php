<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFilmeRequest;
use App\Http\Requests\UpdateFilmeRequest;
use App\Models\Filme;
use Illuminate\Http\JsonResponse;

class FilmeController extends Controller
{
    /**
     * Armazena um novo filme.
     */
    public function store(StoreFilmeRequest $request): JsonResponse
    {
        $filme = Filme::create($request->validated());

        return response()->json([
            'message' => 'Filme criado com sucesso.',
            'data' => $filme,
        ], 201);
    }

    /**
     * Atualiza um filme existente.
     */
    public function update(UpdateFilmeRequest $request, Filme $filme): JsonResponse
    {
        $filme->update($request->validated());

        return response()->json([
            'message' => 'Filme atualizado com sucesso.',
            'data' => $filme->fresh(),
        ]);
    }


    /**
     * Desativa um filme (soft business delete).
     */
    public function destroy(Filme $filme): JsonResponse
    {
        if ($filme->status === 'inativo') {
            return response()->json([
                'message' => 'Filme já está inativo.',
            ], 409);
        }

        $filme->update([
            'status' => 'inativo',
        ]);

        return response()->json([
            'message' => 'Filme desativado com sucesso.',
            'data' => $filme->fresh(),
        ]);
    }

    /**
     * Exibe os detalhes de um filme ativo.
     */
    public function show(Filme $filme): JsonResponse
    {
        if ($filme->status !== 'ativo') {
            abort(404);
        }

        return response()->json([
            'data' => $filme,
        ]);
    }

    /**
     * Lista filmes ativos.
     */
    public function index(): JsonResponse
    {
        $filmes = Filme::query()
            ->where('status', 'ativo')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $filmes,
        ]);
    }
}
