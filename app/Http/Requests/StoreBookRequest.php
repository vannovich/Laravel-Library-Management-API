<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'max:255', 'unique:books,isbn'],
            'description' => ['nullable', 'string'],
            'author_id' => ['required', 'integer', 'exists:authors,id'],
            'genre' => ['nullable', 'string', 'max:255'],
            'published_date' => ['nullable', 'date'],
            'total_copies' => ['required', 'integer', 'min:1'],
            'cover_image' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
