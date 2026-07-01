@extends('layouts.app')

@section('content')
    <div class="mx-auto" style="max-width: 460px;">
        <div class="card page-card">
            <div class="card-body p-4 p-md-5">
                <h1 class="page-title mb-2">Consulta protegida</h1>
                <p class="text-muted mb-4">Informe a senha para acessar a listagem de cadastros.</p>

                <form action="{{ route('consulta.authenticate') }}" method="POST" class="row g-3">
                    @csrf

                    <div class="col-12">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required autofocus>
                    </div>

                    <div class="col-12 d-flex flex-column flex-sm-row gap-2">
                        <button type="submit" class="btn btn-primary">Entrar</button>
                        <a href="{{ route('cadastros.create') }}" class="btn btn-outline-secondary">Voltar ao cadastro</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
