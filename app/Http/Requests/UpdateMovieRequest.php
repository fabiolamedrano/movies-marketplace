<?php

namespace App\Http\Requests;

use App\Enums\MovieFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Requests\Concerns\StripsHtml;

class UpdateMovieRequest extends FormRequest
{
    use StripsHtml;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // "sometimes": en PUT/PATCH solo se valida lo que venga
        return [
            'title'    => ['sometimes', 'string', 'max:255'],
            'genre'    => ['sometimes', 'string', 'max:100'],
            'format'   => ['sometimes', Rule::enum(MovieFormat::class)],
            'price'    => ['sometimes', 'numeric', 'min:0.01', 'max:999999.99'],
            'stock'    => ['sometimes', 'integer', 'min:0'],
            'synopsis' => ['sometimes', 'nullable', 'string'],
        ];
    }

    protected function stripFields(): array
    {
        return ['title', 'genre', 'synopsis'];
    }
}
