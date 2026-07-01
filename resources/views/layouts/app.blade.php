<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Cadastro' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f7fb;
        }

        .page-card {
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.08);
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
        }

        .table-responsive {
            border-radius: 0.75rem;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container py-4 py-md-5">
        <div class="mx-auto" style="max-width: 1180px;">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <div class="fw-semibold mb-2">Revise os campos informados:</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('[data-mask="celular"]').forEach(function (input) {
            input.addEventListener('input', function () {
                let value = this.value.replace(/\D/g, '').slice(0, 11);

                if (value.length > 10) {
                    value = value.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1) $2-$3');
                } else {
                    value = value.replace(/^(\d{2})(\d{4,5})(\d{0,4}).*/, '($1) $2-$3');
                }

                this.value = value.replace(/-$/, '');
            });
        });

        document.querySelectorAll('[data-mask="cep"]').forEach(function (input) {
            input.addEventListener('input', function () {
                let value = this.value.replace(/\D/g, '').slice(0, 8);
                value = value.replace(/^(\d{5})(\d{0,3}).*/, '$1-$2');
                this.value = value.replace(/-$/, '');
            });

            input.addEventListener('blur', async function () {
                const cep = this.value.replace(/\D/g, '');

                if (cep.length !== 8) {
                    return;
                }

                try {
                    const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                    const data = await response.json();

                    if (!data.erro) {
                        const logradouro = document.querySelector('[name="logradouro"]');

                        if (logradouro && !logradouro.value) {
                            logradouro.value = data.logradouro || '';
                        }
                    }
                } catch (error) {
                    console.debug('Falha ao consultar o CEP.', error);
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
