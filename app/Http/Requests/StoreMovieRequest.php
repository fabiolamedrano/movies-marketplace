<?php

namespace App\Http\Requests;

use App\Enums\MovieFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Requests\Concerns\StripsHtml;

class StoreMovieRequest extends FormRequest
{
    use StripsHtml; // limpia etiqueta html antes de validar (defensa contra XSS)

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'    => ['required', 'string', 'max:255'],
            'genre'    => ['required', 'string', 'max:100'],
            // el formato se valida contra un enum asi solo se pone bluray, dvd o vhs
            'format'   => ['required', Rule::enum(MovieFormat::class)],
            'price'    => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'stock'    => ['required', 'integer', 'min:0'],
            'synopsis' => ['nullable', 'string'],
        ];
    }

    // Estos son los campos de texto libre que un atacante podria atadcar
    protected function stripFields(): array
    {
        return ['title', 'genre', 'synopsis'];
    }
}