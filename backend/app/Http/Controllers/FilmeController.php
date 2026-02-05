<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFilmeRequest;
use App\Http\Requests\UpdateFilmeRequest;
use App\Models\Filme;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Throwable;

class FilmeController extends Controller
{
    /**
     * Armazena um novo filme.
     */
    public function store(StoreFilmeRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (empty($data['status'])) {
            $data['status'] = 'ativo';
        }

        try {
            $filme = DB::transaction(function () use ($data) {
                return Filme::create($data);
            });

            return response()->json([
                'message' => 'Filme criado com sucesso.',
                'data' => $filme->fresh(),
            ], 201);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erro ao criar filme.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Atualiza um filme existente.
     */
    public function update(UpdateFilmeRequest $request, Filme $filme): JsonResponse
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($filme, $data) {
                $filme->update($data);
            });

            return response()->json([
                'message' => 'Filme atualizado com sucesso.',
                'data' => $filme->fresh(),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erro ao atualizar filme.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Desativa um filme (soft business delete).
     */
    public function destroy(Filme $filme): JsonResponse
    {
        if ($filme->status === 'inativo') {
            return response()->json(['message' => 'Filme já está inativo.'], 409);
        }

        try {
            DB::transaction(function () use ($filme) {
                $filme->update(['status' => 'inativo']);
            });

            return response()->json([
                'message' => 'Filme desativado com sucesso.',
                'data' => $filme->fresh(),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erro ao desativar filme.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Ativa um filme (soft business delete).
     */
    public function activate(Filme $filme): JsonResponse
    {
        if ($filme->status === 'ativo') {
            return response()->json([
                'message' => 'Filme já está ativo.',
            ], 409);
        }

        try {
            DB::transaction(function () use ($filme) {
                $filme->update(['status' => 'ativo']);
            });

            return response()->json([
                'message' => 'Filme reativado com sucesso.',
                'data' => $filme->fresh(),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erro ao reativar filme.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Exibe os detalhes de um filme ativo.
     */
    public function show(Filme $filme): JsonResponse
    {
        if ($filme->status !== 'ativo') {
            return response()->json(['message' => 'Filme não encontrado.'], 404);
        }

        return response()->json(['data' => $filme]);
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
