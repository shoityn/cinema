<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFilmeRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    
    //Regras de validação para criação de filme.

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],

            'sinopse' => ['nullable', 'string'],

            'data_lancamento' => ['nullable', 'date'],

            'duracao_minutos' => ['nullable', 'integer', 'min:1'],

            'trailer_url' => ['nullable', 'string', 'max:500'],

            'poster_url' => ['nullable', 'string', 'max:255'],

            'backdrop_url' => ['nullable', 'string', 'max:255'],

            'status' => ['nullable', 'in:ativo,inativo'],

            'tmdb_id' => ['nullable', 'integer', 'unique:filmes,tmdb_id'],
        ];
    }
}
