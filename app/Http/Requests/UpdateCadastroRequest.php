<?php

namespace App\Http\Requests;

use App\Models\Cadastro;
use Illuminate\Validation\Rule;

class UpdateCadastroRequest extends StoreCadastroRequest
{
    public function rules(): array
    {
        $cadastro = $this->route('cadastro');

        return [
            'codigo' => ['required', 'string', 'max:50', Rule::unique('cadastros', 'codigo')->ignore($cadastro)],
            'nome' => ['required', 'string', 'max:255'],
            'apelido' => ['required', 'string', 'max:255'],
            'ra' => ['required', 'string', 'max:255'],
            'cep' => ['required', 'regex:/^\d{8}$/'],
            'logradouro' => ['required', 'string', 'max:255'],
            'numero' => ['required', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:255'],
            'celular' => ['required', 'regex:/^(?:55)?[1-9]{2}9\d{8}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'lideranca' => ['nullable', 'string', 'max:255'],
            'atualizado_por' => ['required', 'string', 'max:255'],
            'tipo_contato' => ['required', Rule::in(array_keys(Cadastro::tiposContato()))],
        ];
    }
}
