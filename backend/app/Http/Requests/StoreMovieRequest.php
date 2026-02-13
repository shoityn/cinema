<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovieRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],

            'overview' => ['nullable', 'string'],

            'release_date' => ['nullable', 'date'],

            'duration_minutes' => ['nullable', 'integer', 'min:1'],

            'trailer_url' => ['nullable', 'string', 'max:500'],

            'poster_url' => ['nullable', 'string', 'max:255'],

            'backdrop_url' => ['nullable', 'string', 'max:255'],

            'status' => ['nullable', 'in:draft,published,archived'],

            'tmdb_id' => ['nullable', 'integer', 'unique:movies,tmdb_id'],
        ];
    }
}
