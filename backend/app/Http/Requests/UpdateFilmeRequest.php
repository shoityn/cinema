<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFilmeRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    ///Regras de validação para atualização de filme.
    
    public function rules(): array
    {
        return [
            'titulo' => ['sometimes', 'required', 'string', 'max:255'],

            'sinopse' => ['sometimes', 'nullable', 'string'],

            'data_lancamento' => ['sometimes', 'nullable', 'date'],

            'duracao_minutos' => ['sometimes', 'nullable', 'integer', 'min:1'],

            'trailer_url' => ['sometimes', 'nullable', 'string', 'max:500'],

            'poster_url' => ['sometimes', 'nullable', 'string', 'max:255'],

            'backdrop_url' => ['sometimes', 'nullable', 'string', 'max:255'],

            'status' => ['sometimes', 'required', 'in:ativo,inativo'],

            // tmdb_id NÃO pode ser atualizado
            'tmdb_id' => ['prohibited'],
        ];
    }
}