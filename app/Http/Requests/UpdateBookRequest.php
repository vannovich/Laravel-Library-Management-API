<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'isbn' => [
                'required',
                'string',
                'max:255',
                Rule::unique('books', 'isbn')->ignore($this->route('book')->id),
            ],
            'description' => ['nullable', 'string'],
            'author_id' => ['required', 'integer', 'exists:authors,id'],
            'genre' => ['nullable', 'string', 'max:255'],
            'published_date' => ['nullable', 'date'],
            'total_copies' => ['required', 'integer', 'min:1'],
            'available_copies' => ['nullable', 'integer', 'min:0'],
            'cover_image' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:available,unavailable'],
        ];
    }
}
