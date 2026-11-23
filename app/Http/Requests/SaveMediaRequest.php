<?php

namespace App\Http\Requests;

use App\Models\Media;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Demo tanpa autentikasi; produksi harus menambah otorisasi.
    }

    public function rules(): array
    {
        $partial = $this->isMethod('patch');
        $presence = $partial ? 'sometimes' : 'required';
        $media = $this->route('media');
        $mediaId = $media instanceof Media ? $media->getKey() : null;

        return [
            'title' => [
                $presence, 'required', 'string', 'max:150',
                Rule::unique('media', 'title')->ignore($mediaId),
            ],
            'type' => [$presence, 'required', 'string', Rule::in(['film', 'series', 'game', 'music'])],
            'genre' => [$presence, 'required', 'string', 'max:100'],
            'release_year' => ['sometimes', 'nullable', 'integer', 'between:1888,2100'],
            'description' => ['sometimes', 'nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul media wajib diisi.',
            'title.unique' => 'Judul media sudah terdaftar.',
            'type.required' => 'Jenis media wajib diisi.',
            'type.in' => 'Jenis media harus film, series, game, atau music.',
            'genre.required' => 'Genre media wajib diisi.',
        ];
    }
}
