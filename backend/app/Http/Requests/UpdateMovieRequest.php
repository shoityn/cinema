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

    protected function prepareForValidation(): void
    {
        // Allow genres sent as JSON string in FormData to be decoded before validation
        if ($this->has('genres') && is_string($this->input('genres'))) {
            $decoded = json_decode($this->input('genres'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->merge(['genres' => $decoded]);
            }
        }
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
