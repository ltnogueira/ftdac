<?php

namespace App\Http\Requests;

use App\Models\Cadastro;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreCadastroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'codigo' => $this->normalizeText($this->input('codigo')),
            'nome' => $this->normalizeText($this->input('nome')),
            'apelido' => $this->normalizeText($this->input('apelido')),
            'ra' => $this->normalizeText($this->input('ra')),
            'cep' => preg_replace('/\D+/', '', (string) $this->input('cep')),
            'logradouro' => $this->normalizeText($this->input('logradouro')),
            'numero' => $this->normalizeText($this->input('numero')),
            'complemento' => $this->normalizeText($this->input('complemento')),
            'celular' => preg_replace('/\D+/', '', (string) $this->input('celular')),
            'email' => $this->normalizeEmail($this->input('email')),
            'lideranca' => $this->normalizeText($this->input('lideranca')),
            'atualizado_por' => $this->normalizeText($this->input('atualizado_por')),
            'tipo_contato' => $this->input('tipo_contato'),
        ]);
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:50', 'unique:cadastros,codigo'],
            'nome' => ['required', 'string', 'max:255'],
            'apelido' => ['required', 'string', 'max:255'],
            'ra' => ['required', 'string', 'max:255'],
            'cep' => ['required', 'regex:/^\d{8}$/'],
            'logradouro' => ['required', 'string', 'max:255'],
            'numero' => ['required', 'string', 'max:20'],
            'complemento' => ['required', 'string', 'max:255'],
            'celular' => ['required', 'regex:/^(?:55)?[1-9]{2}9\d{8}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'lideranca' => ['nullable', 'string', 'max:255'],
            'atualizado_por' => ['required', 'string', 'max:255'],
            'tipo_contato' => ['required', Rule::in(array_keys(Cadastro::tiposContato()))],
        ];
    }

    public function attributes(): array
    {
        return [
            'codigo' => 'codigo',
            'nome' => 'nome',
            'apelido' => 'apelido',
            'ra' => 'RA - Regiao Administrativa',
            'cep' => 'CEP',
            'logradouro' => 'logradouro',
            'numero' => 'numero',
            'complemento' => 'complemento',
            'celular' => 'celular',
            'email' => 'e-mail',
            'lideranca' => 'lideranca',
            'atualizado_por' => 'atualizado por',
            'tipo_contato' => 'tipo de contato',
        ];
    }

    public function messages(): array
    {
        return [
            'cep.regex' => 'O campo CEP deve conter 8 digitos.',
            'celular.regex' => 'Informe um celular brasileiro valido com DDD.',
            'tipo_contato.in' => 'Selecione um tipo de contato valido.',
        ];
    }

    protected function normalizeText(mixed $value): ?string
    {
        $normalized = Str::of((string) $value)
            ->trim()
            ->ascii()
            ->upper()
            ->toString();

        return $normalized === '' ? null : $normalized;
    }

    protected function normalizeEmail(mixed $value): ?string
    {
        $normalized = Str::of((string) $value)
            ->trim()
            ->ascii()
            ->lower()
            ->toString();

        return $normalized === '' ? null : $normalized;
    }
}
