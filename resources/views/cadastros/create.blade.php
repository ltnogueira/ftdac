@extends('layouts.app')

@section('content')
    <div class="ftdac-shell">
        <section class="ftdac-hero">
            <div>
                <span class="ftdac-badge">FTDAC</span>
                <h1>Atualização Cadastral</h1>
            </div>
            <div class="ftdac-hero-card">
                <small>Campos com * são obrigatórios</small>
            </div>
        </section>

        <section class="ftdac-card">
            @include('cadastros.form', ['mode' => 'create'])
        </section>
    </div>
@endsection
