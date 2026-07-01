@extends('layouts.app')

@section('content')
    <div class="ftdac-shell">
        <section class="ftdac-hero">
            <div>
                <span class="ftdac-badge">FTDAC</span>
                <h1>Editar cadastro - FTDAC</h1>
                <p>Atualize as informações do registro selecionado mantendo o fluxo cadastral organizado.</p>
            </div>
            <div class="ftdac-hero-card">
                <span>Atualização</span>
                <strong>Editar registro</strong>
                <small>Revise antes de salvar</small>
            </div>
        </section>

        <section class="ftdac-card">
            @include('cadastros.form', ['mode' => 'edit'])
        </section>
    </div>
@endsection
