<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsultaLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'senha' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'senha' => 'senha',
        ];
    }
}
