@once
    <style>
        .ftdac-shell {
            max-width: 1180px;
            margin: 1.5rem auto 3rem;
        }

        .ftdac-hero {
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            gap: 1.25rem;
            margin-bottom: 1.25rem;
            padding: 2rem;
            color: #fff;
            border-radius: 28px;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.22), transparent 34%),
                linear-gradient(135deg, #0f172a 0%, #1d4ed8 56%, #0284c7 100%);
            box-shadow: 0 24px 70px rgba(15, 23, 42, .18);
            overflow: hidden;
        }

        .ftdac-hero h1 {
            max-width: 820px;
            margin: .75rem 0 .65rem;
            font-size: clamp(1.55rem, 3vw, 2.45rem);
            line-height: 1.1;
            font-weight: 800;
            letter-spacing: -.035em;
        }

        .ftdac-hero p {
            max-width: 760px;
            margin: 0;
            color: rgba(255,255,255,.82);
            font-size: 1rem;
        }

        .ftdac-badge {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            width: fit-content;
            padding: .42rem .75rem;
            border: 1px solid rgba(255,255,255,.28);
            border-radius: 999px;
            background: rgba(255,255,255,.13);
            color: #fff;
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .12em;
        }

        .ftdac-hero-card {
            min-width: 210px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: .25rem;
            padding: 1.15rem;
            border: 1px solid rgba(255,255,255,.24);
            border-radius: 22px;
            background: rgba(255,255,255,.14);
            backdrop-filter: blur(10px);
        }

        .ftdac-hero-card span,
        .ftdac-hero-card small {
            color: rgba(255,255,255,.75);
        }

        .ftdac-hero-card strong {
            font-size: 1.15rem;
        }

        .ftdac-card {
            padding: 1.35rem;
            border: 1px solid #e5e7eb;
            border-radius: 28px;
            background: rgba(255,255,255,.94);
            box-shadow: 0 24px 70px rgba(15, 23, 42, .09);
        }

        .ftdac-form {
            display: flex;
            flex-direction: column;
            gap: 1.15rem;
        }

        .ftdac-section {
            padding: 1.25rem;
            border: 1px solid #eef2f7;
            border-radius: 22px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }

        .ftdac-section-header {
            display: flex;
            gap: .85rem;
            align-items: center;
            margin-bottom: 1rem;
        }

        .ftdac-section-number {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            border-radius: 14px;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 800;
            font-size: .9rem;
        }

        .ftdac-section-header h2 {
            margin: 0;
            color: #0f172a;
            font-size: 1rem;
            font-weight: 800;
        }

        .ftdac-section-header p {
            margin: .15rem 0 0;
            color: #64748b;
            font-size: .875rem;
        }

        .ftdac-card .form-label {
            color: #334155;
            font-size: .86rem;
            font-weight: 700;
            margin-bottom: .42rem;
        }

        .ftdac-card .form-control {
            min-height: 46px;
            border-color: #dbe3ef;
            border-radius: 14px;
            color: #0f172a;
            box-shadow: none;
            transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        }

        .ftdac-card .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 .22rem rgba(37, 99, 235, .11);
        }

        .ftdac-radio-group {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .75rem;
        }

        .ftdac-radio-card {
            display: flex;
            align-items: flex-start;
            gap: .65rem;
            min-height: 70px;
            padding: .85rem;
            border: 1px solid #dbe3ef;
            border-radius: 16px;
            background: #fff;
            cursor: pointer;
            transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        }

        .ftdac-radio-card:hover {
            transform: translateY(-1px);
            border-color: #93c5fd;
            box-shadow: 0 12px 30px rgba(37, 99, 235, .08);
        }

        .ftdac-radio-card .form-check-input {
            margin-top: .2rem;
        }

        .ftdac-radio-card strong {
            display: block;
            color: #0f172a;
            font-size: .95rem;
            line-height: 1.1;
        }

        .ftdac-radio-card small {
            display: block;
            margin-top: .18rem;
            color: #64748b;
            font-size: .78rem;
        }

        .ftdac-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .65rem;
            padding-top: .35rem;
        }

        .ftdac-actions .btn {
            min-height: 44px;
            border-radius: 14px;
            font-weight: 700;
        }

        @media (max-width: 767.98px) {
            .ftdac-shell {
                margin-top: .75rem;
            }

            .ftdac-hero {
                flex-direction: column;
                padding: 1.35rem;
                border-radius: 22px;
            }

            .ftdac-hero-card {
                min-width: 0;
            }

            .ftdac-card {
                padding: .9rem;
                border-radius: 22px;
            }

            .ftdac-section {
                padding: 1rem;
            }

            .ftdac-radio-group {
                grid-template-columns: 1fr;
            }

            .ftdac-actions .btn,
            .ftdac-actions a {
                width: 100%;
            }
        }
    </style>
@endonce

@php
    $isEdit = ($mode ?? 'create') === 'edit';
    $action = $isEdit ? route('cadastros.update', $cadastro) : route('cadastros.store');
    $emailAtual = old('email', data_get($cadastro ?? null, 'email'));
    $atualizadoPorAtual = old('atualizado_por', data_get($cadastro ?? null, 'atualizado_por') ?: \App\Models\Cadastro::DEFAULT_ATUALIZADO_POR);
    $tipoContatoAtual = old('tipo_contato', data_get($cadastro ?? null, 'tipo_contato') ?: \App\Models\Cadastro::DEFAULT_TIPO_CONTATO);
@endphp

<form action="{{ $action }}" method="POST" class="ftdac-form">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 mb-0" role="alert">
            <strong>Confira os campos:</strong> existem informações obrigatórias ou inválidas para corrigir.
        </div>
    @endif

    <input type="hidden" name="email" value="{{ $emailAtual }}">
    <input type="hidden" name="atualizado_por" value="{{ $atualizadoPorAtual }}">
    <input type="hidden" name="tipo_contato" value="{{ $tipoContatoAtual }}">

    <div class="ftdac-section">
        <div class="ftdac-section-header">
            <span class="ftdac-section-number">01</span>
            <div>
                <h2>Identificação do cadastro</h2>
                <p>Informe os dados principais da pessoa cadastrada.</p>
            </div>
        </div>

        <div class="row g-3">
            @if ($isEdit)
                <div class="col-12 col-md-4">
                    <label for="codigo" class="form-label">Código</label>
                    <input type="text" class="form-control @error('codigo') is-invalid @enderror" id="codigo" name="codigo" value="{{ old('codigo', data_get($cadastro ?? null, 'codigo')) }}" readonly>
                    @error('codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            @else
                <div class="col-12">
                    <div class="alert alert-primary rounded-4 mb-0" role="alert">
                        <strong>Código automático:</strong> o sistema gera o número sequencial do cadastro no momento do salvamento.
                    </div>
                </div>
            @endif

            <div class="col-12 {{ $isEdit ? 'col-md-8' : '' }}">
                <label for="nome" class="form-label">Nome *</label>
                <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" value="{{ old('nome', data_get($cadastro ?? null, 'nome')) }}" placeholder="Nome completo" required>
                @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-6">
                <label for="apelido" class="form-label">Apelido</label>
                <input type="text" class="form-control @error('apelido') is-invalid @enderror" id="apelido" name="apelido" value="{{ old('apelido', data_get($cadastro ?? null, 'apelido')) }}" placeholder="Como é conhecido(a)">
                @error('apelido') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-6">
                <label for="ra" class="form-label">RA - Região Administrativa *</label>
                <input type="text" class="form-control @error('ra') is-invalid @enderror" id="ra" name="ra" value="{{ old('ra', data_get($cadastro ?? null, 'ra')) }}" placeholder="Ex.: Ceilandia, Plano Piloto, Samambaia" required>
                @error('ra') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    <div class="ftdac-section">
        <div class="ftdac-section-header">
            <span class="ftdac-section-number">02</span>
            <div>
                <h2>Endereço</h2>
                <p>Preencha o endereço quando essa informação estiver disponível.</p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-3">
                <label for="cep" class="form-label">CEP *</label>
                <input type="text" class="form-control @error('cep') is-invalid @enderror" id="cep" name="cep" value="{{ old('cep', data_get($cadastro ?? null, 'cep')) }}" placeholder="00000-000" data-mask="cep" inputmode="numeric" required>
                @error('cep') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-7">
                <label for="logradouro" class="form-label">Logradouro *</label>
                <input type="text" class="form-control @error('logradouro') is-invalid @enderror" id="logradouro" name="logradouro" value="{{ old('logradouro', data_get($cadastro ?? null, 'logradouro')) }}" placeholder="Rua, avenida, travessa..." required>
                @error('logradouro') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-2">
                <label for="numero" class="form-label">Número *</label>
                <input type="text" class="form-control @error('numero') is-invalid @enderror" id="numero" name="numero" value="{{ old('numero', data_get($cadastro ?? null, 'numero')) }}" placeholder="Nº" required>
                @error('numero') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label for="complemento" class="form-label">Complemento</label>
                <input type="text" class="form-control @error('complemento') is-invalid @enderror" id="complemento" name="complemento" value="{{ old('complemento', data_get($cadastro ?? null, 'complemento')) }}" placeholder="Apartamento, bloco, referência...">
                @error('complemento') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    <div class="ftdac-section">
        <div class="ftdac-section-header">
            <span class="ftdac-section-number">03</span>
            <div>
                <h2>Contato</h2>
                <p>Informe o celular principal e o coordenador deste cadastro.</p>
            </div>
        </div>

        <div class="row g-3 align-items-start">
            <div class="col-12 col-md-6">
                <label for="celular" class="form-label">Celular *</label>
                <input type="text" class="form-control @error('celular') is-invalid @enderror" id="celular" name="celular" value="{{ old('celular', data_get($cadastro ?? null, 'celular')) }}" placeholder="(00) 00000-0000" data-mask="celular" inputmode="numeric" required>
                @error('celular') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-6">
                <label for="lideranca" class="form-label">Coordenador</label>
                <input type="text" class="form-control @error('lideranca') is-invalid @enderror" id="lideranca" name="lideranca" value="{{ old('lideranca', data_get($cadastro ?? null, 'lideranca')) }}" placeholder="Nome do coordenador">
                @error('lideranca') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    <div class="ftdac-actions">
        <button type="submit" class="btn btn-primary px-4">
            {{ $isEdit ? 'Salvar alterações' : 'Salvar cadastro' }}
        </button>

        @if ($isEdit)
            <a href="{{ route('cadastros.index') }}" class="btn btn-outline-secondary px-4">Voltar para consulta</a>
        @else
            <button type="reset" class="btn btn-outline-secondary px-4">Limpar formulário</button>
            <a href="{{ route('consulta.login') }}" class="btn btn-outline-dark px-4">Acessar consulta</a>
        @endif
    </div>
</form>
