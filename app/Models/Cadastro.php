<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cadastro extends Model
{
    public const TIPO_VISITA = 'visita';
    public const TIPO_LIGACAO = 'ligacao';

    protected $fillable = [
        'codigo',
        'nome',
        'apelido',
        'ra',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'celular',
        'email',
        'lideranca',
        'atualizado_por',
        'tipo_contato',
    ];

    public static function tiposContato(): array
    {
        return [
            self::TIPO_VISITA => 'Visita',
            self::TIPO_LIGACAO => 'Ligacao',
        ];
    }

    public function scopeFiltrar(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['codigo'] ?? null, fn (Builder $builder, string $value) => $builder->where('codigo', 'like', '%'.self::normalizeText($value).'%'))
            ->when($filters['nome'] ?? null, fn (Builder $builder, string $value) => $builder->where('nome', 'like', '%'.self::normalizeText($value).'%'))
            ->when($filters['ra'] ?? null, fn (Builder $builder, string $value) => $builder->where('ra', self::normalizeText($value)))
            ->when($filters['celular'] ?? null, fn (Builder $builder, string $value) => $builder->where('celular', 'like', "%{$value}%"))
            ->when($filters['lideranca'] ?? null, fn (Builder $builder, string $value) => $builder->where('lideranca', 'like', '%'.self::normalizeText($value).'%'))
            ->when($filters['tipo_contato'] ?? null, fn (Builder $builder, string $value) => $builder->where('tipo_contato', $value))
            ->when($filters['atualizado_por'] ?? null, fn (Builder $builder, string $value) => $builder->where('atualizado_por', 'like', '%'.self::normalizeText($value).'%'))
            ->when($filters['data_inicial'] ?? null, fn (Builder $builder, string $value) => $builder->whereDate('created_at', '>=', $value))
            ->when($filters['data_final'] ?? null, fn (Builder $builder, string $value) => $builder->whereDate('created_at', '<=', $value));
    }

    public static function normalizeText(mixed $value): string
    {
        return Str::of((string) $value)
            ->trim()
            ->ascii()
            ->upper()
            ->toString();
    }
}
