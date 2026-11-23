<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddMediaToCollectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Hanya untuk kebutuhan demo Modul 4.
    }

    public function rules(): array
    {
        return [
            'media_id' => ['required', 'integer', 'exists:media,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'media_id.required' => 'ID media wajib diisi.',
            'media_id.exists' => 'Media yang dipilih tidak ditemukan.',
        ];
    }
}
