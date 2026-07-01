@extends('layouts.app')

@section('content')
    <div class="card page-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                <div>
                    <h1 class="page-title mb-1">Consulta de cadastros</h1>
                    <p class="text-muted mb-0">Filtre, visualize, edite e exporte os registros cadastrados.</p>
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="{{ route('cadastros.export', request()->query()) }}" class="btn btn-success">Exportar XLSX</a>
                    <a href="{{ route('cadastros.create') }}" class="btn btn-outline-primary">Novo cadastro</a>
                    <form action="{{ route('consulta.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-dark w-100">Sair</button>
                    </form>
                </div>
            </div>

            <form method="GET" action="{{ route('cadastros.index') }}" class="row g-3">
                <div class="col-12 col-md-3">
                    <label for="codigo" class="form-label">Codigo</label>
                    <input type="text" class="form-control" id="codigo" name="codigo" value="{{ $filters['codigo'] ?? '' }}">
                </div>

                <div class="col-12 col-md-3">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome" value="{{ $filters['nome'] ?? '' }}">
                </div>

                <div class="col-12 col-md-3">
                    <label for="ra" class="form-label">RA</label>
                    <input type="text" class="form-control" id="ra" name="ra" value="{{ $filters['ra'] ?? '' }}">
                </div>

                <div class="col-12 col-md-3">
                    <label for="celular" class="form-label">Celular</label>
                    <input type="text" class="form-control" id="celular" name="celular" value="{{ $filters['celular'] ?? '' }}" data-mask="celular">
                </div>

                <div class="col-12 col-md-3">
                    <label for="lideranca" class="form-label">Lideranca</label>
                    <input type="text" class="form-control" id="lideranca" name="lideranca" value="{{ $filters['lideranca'] ?? '' }}">
                </div>

                <div class="col-12 col-md-3">
                    <label for="tipo_contato" class="form-label">Tipo de contato</label>
                    <select id="tipo_contato" name="tipo_contato" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($tiposContato as $valor => $label)
                            <option value="{{ $valor }}" @selected(($filters['tipo_contato'] ?? '') === $valor)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label for="atualizado_por" class="form-label">Atualizado por</label>
                    <input type="text" class="form-control" id="atualizado_por" name="atualizado_por" value="{{ $filters['atualizado_por'] ?? '' }}">
                </div>

                <div class="col-12 col-md-3">
                    <label for="data_inicial" class="form-label">Data inicial</label>
                    <input type="date" class="form-control" id="data_inicial" name="data_inicial" value="{{ $filters['data_inicial'] ?? '' }}">
                </div>

                <div class="col-12 col-md-3">
                    <label for="data_final" class="form-label">Data final</label>
                    <input type="date" class="form-control" id="data_final" name="data_final" value="{{ $filters['data_final'] ?? '' }}">
                </div>

                <div class="col-12 d-flex flex-column flex-sm-row gap-2">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="{{ route('cadastros.index') }}" class="btn btn-outline-secondary">Limpar filtros</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card page-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Codigo</th>
                            <th>Nome</th>
                            <th>Apelido</th>
                            <th>RA</th>
                            <th>Celular</th>
                            <th>E-mail</th>
                            <th>Lideranca</th>
                            <th>Tipo</th>
                            <th>Atualizado por</th>
                            <th>Data do cadastro</th>
                            <th class="text-end">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cadastros as $cadastro)
                            <tr>
                                <td>{{ $cadastro->codigo }}</td>
                                <td>{{ $cadastro->nome }}</td>
                                <td>{{ $cadastro->apelido ?: '-' }}</td>
                                <td>{{ $cadastro->ra ?: '-' }}</td>
                                <td>{{ $cadastro->celular }}</td>
                                <td>{{ $cadastro->email ?: '-' }}</td>
                                <td>{{ $cadastro->lideranca }}</td>
                                <td>{{ $tiposContato[$cadastro->tipo_contato] ?? $cadastro->tipo_contato }}</td>
                                <td>{{ $cadastro->atualizado_por }}</td>
                                <td>{{ optional($cadastro->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('cadastros.edit', $cadastro) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                        <form action="{{ route('cadastros.destroy', $cadastro) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este cadastro?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4 text-muted">Nenhum registro encontrado para os filtros informados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($cadastros->hasPages())
            <div class="card-footer bg-white">
                {{ $cadastros->links() }}
            </div>
        @endif
    </div>
@endsection
