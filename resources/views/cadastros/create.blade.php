@extends('layouts.app')

@section('content')
    <div class="ftdac-shell">
        <section class="ftdac-hero">
            <div>
                <span class="ftdac-badge">FTDAC</span>
                <h1>Fluxo de Trabalho Diretrizes de Atualização Cadastral - FTDAC</h1>
                <p>Preencha as informações abaixo para registrar uma nova atualização cadastral de forma rápida e organizada.</p>
            </div>
            <div class="ftdac-hero-card">
                <span>Cadastro</span>
                <strong>Novo registro</strong>
                <small>Campos com * são obrigatórios</small>
            </div>
        </section>

        <section class="ftdac-card">
            @include('cadastros.form', ['mode' => 'create'])
        </section>
    </div>
@endsection
