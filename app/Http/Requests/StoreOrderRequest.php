<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items'              => ['required', 'array', 'min:1'],
            'items.*.movie_id'   => ['required', 'integer', 'exists:movies,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }
}