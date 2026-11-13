<?php
// app/Http/Requests/RegisterRequest.php
namespace App\Http\Requests;

use App\Enums\SellerType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Http\Requests\Concerns\StripsHtml;

class RegisterRequest extends FormRequest
{
    use StripsHtml;

    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'    => ['required', 'confirmed', Password::defaults()],
            'seller_type' => ['sometimes', Rule::enum(SellerType::class)],
        ];
    }

    protected function stripFields(): array
    {
        return ['name'];
    }
}
