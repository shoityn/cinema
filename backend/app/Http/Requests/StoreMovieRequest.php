<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovieRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => ['required', 'string'],
            'overview' => ['nullable', 'string'],
            'release_date' => ['nullable', 'date'],
            'duration_minutes' => ['nullable', 'integer'],
            'tmdb_id' => ['nullable', 'integer'],

            'genres' => ['required', 'array'],
            'genres.*' => ['integer', 'exists:genres,genre_id'],

            'media' => ['required', 'array'],

            // poster/backdrop/logo/trailer groups
            'media.poster' => ['nullable', 'array'],
            'media.poster.provider' => ['required_with:media.poster', 'in:tmdb,local'],
            'media.poster.path' => ['nullable', 'string'],
            'media.poster.external_key' => ['nullable', 'string'],

            'media.backdrop' => ['nullable', 'array'],
            'media.backdrop.provider' => ['required_with:media.backdrop', 'in:tmdb,local'],
            'media.backdrop.path' => ['nullable', 'string'],
            'media.backdrop.external_key' => ['nullable', 'string'],

            'media.logo' => ['nullable', 'array'],
            'media.logo.provider' => ['required_with:media.logo', 'in:tmdb,local'],
            'media.logo.path' => ['nullable', 'string'],
            'media.logo.external_key' => ['nullable', 'string'],

            'media.trailer' => ['nullable', 'array'],
            'media.trailer.provider' => ['required_with:media.trailer', 'in:tmdb,local'],
            'media.trailer.path' => ['nullable', 'string'],
            'media.trailer.external_key' => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'genres.*.exists' => 'One or more genres do not exist.',
        ];
    }
}
// <?php

// namespace App\Http\Requests;

// use Illuminate\Foundation\Http\FormRequest;

// class StoreMovieRequest extends FormRequest
// {
//     public function authorize(): bool
//     {
//         return true;
//     }

//     public function rules(): array
//     {
//         return [
//             'title' => ['required', 'string', 'max:255'],

//             'overview' => ['nullable', 'string'],

//             'release_date' => ['nullable', 'date'],

//             'duration_minutes' => ['nullable', 'integer', 'min:1'],

//             'trailer_url' => ['nullable', 'string', 'max:500'],

//             'poster_url' => ['nullable', 'string', 'max:255'],

//             'backdrop_url' => ['nullable', 'string', 'max:255'],

//             'status' => ['nullable', 'in:draft,published,archived'],

//             'tmdb_id' => ['nullable', 'integer', 'unique:movies,tmdb_id'],
//         ];
//     }
// }
