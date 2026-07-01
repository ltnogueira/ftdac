<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCadastroRequest;
use App\Http\Requests\UpdateCadastroRequest;
use App\Models\Cadastro;
use App\Support\SimpleXlsxExporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CadastroController extends Controller
{
    public function create(): View
    {
        return view('cadastros.create', [
            'cadastro' => new Cadastro(),
            'tiposContato' => Cadastro::tiposContato(),
        ]);
    }

    public function store(StoreCadastroRequest $request): RedirectResponse
    {
        Cadastro::create($request->validated());

        return redirect()
            ->route('cadastros.create')
            ->with('success', 'Cadastro realizado com sucesso.');
    }

    public function index(Request $request): View
    {
        $filters = $this->filters($request);

        $cadastros = Cadastro::query()
            ->filtrar($filters)
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('consulta.index', [
            'cadastros' => $cadastros,
            'filters' => $filters,
            'tiposContato' => Cadastro::tiposContato(),
        ]);
    }

    public function edit(Cadastro $cadastro): View
    {
        return view('cadastros.edit', [
            'cadastro' => $cadastro,
            'tiposContato' => Cadastro::tiposContato(),
        ]);
    }

    public function update(UpdateCadastroRequest $request, Cadastro $cadastro): RedirectResponse
    {
        $cadastro->update($request->validated());

        return redirect()
            ->route('cadastros.index')
            ->with('success', 'Cadastro atualizado com sucesso.');
    }

    public function export(Request $request)
    {
        $filters = $this->filters($request);

        $rows = Cadastro::query()
            ->filtrar($filters)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Cadastro $cadastro) => [
                $cadastro->codigo,
                $cadastro->nome,
                $cadastro->apelido,
                $cadastro->ra,
                $cadastro->celular,
                $cadastro->email,
                $cadastro->lideranca,
                Cadastro::tiposContato()[$cadastro->tipo_contato] ?? $cadastro->tipo_contato,
                $cadastro->atualizado_por,
                optional($cadastro->created_at)->format('d/m/Y H:i'),
            ])
            ->all();

        return SimpleXlsxExporter::download(
            'cadastros.xlsx',
            ['Codigo', 'Nome', 'Apelido', 'RA', 'Celular', 'E-mail', 'Lideranca', 'Tipo de contato', 'Atualizado por', 'Data do cadastro'],
            $rows
        );
    }

    public function destroy(Cadastro $cadastro): RedirectResponse
    {
        $cadastro->delete();

        return redirect()
            ->route('cadastros.index')
            ->with('success', 'Cadastro excluido com sucesso.');
    }

    private function filters(Request $request): array
    {
        $filters = $request->only([
            'codigo',
            'nome',
            'ra',
            'celular',
            'lideranca',
            'tipo_contato',
            'atualizado_por',
            'data_inicial',
            'data_final',
        ]);

        $filters['codigo'] = $this->normalizeText($filters['codigo'] ?? '');
        $filters['nome'] = $this->normalizeText($filters['nome'] ?? '');
        $filters['ra'] = $this->normalizeText($filters['ra'] ?? '');
        $filters['celular'] = preg_replace('/\D+/', '', (string) ($filters['celular'] ?? ''));
        $filters['lideranca'] = $this->normalizeText($filters['lideranca'] ?? '');
        $filters['tipo_contato'] = trim((string) ($filters['tipo_contato'] ?? ''));
        $filters['atualizado_por'] = $this->normalizeText($filters['atualizado_por'] ?? '');
        $filters['data_inicial'] = trim((string) ($filters['data_inicial'] ?? ''));
        $filters['data_final'] = trim((string) ($filters['data_final'] ?? ''));

        return array_filter($filters, fn ($value) => $value !== '');
    }

    private function normalizeText(mixed $value): string
    {
        return Str::of((string) $value)
            ->trim()
            ->ascii()
            ->upper()
            ->toString();
    }
}
