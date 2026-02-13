<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMovieRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],

            'overview' => ['sometimes', 'nullable', 'string'],

            'release_date' => ['sometimes', 'nullable', 'date'],

            'duration_minutes' => ['sometimes', 'nullable', 'integer', 'min:1'],

            'trailer_url' => ['sometimes', 'nullable', 'string', 'max:500'],

            'poster_url' => ['sometimes', 'nullable', 'string', 'max:255'],

            'backdrop_url' => ['sometimes', 'nullable', 'string', 'max:255'],

            'status' => ['sometimes', 'required', 'in:draft,published,archived'],

            'tmdb_id' => ['prohibited'],
        ];
    }
}
