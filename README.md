# Cadastro Simples com Consulta

Aplicacao Laravel 12 para cadastro publico de registros e consulta protegida por senha simples, com filtros, edicao e exportacao em XLSX.

## Requisitos

- PHP 8.2
- Composer
- MySQL em execucao

## Configuracao local

1. Ajuste o arquivo `.env` conforme o seu MySQL.
2. Garanta que exista um banco chamado `ftdac` ou altere `DB_DATABASE`.
3. Defina a senha de acesso da consulta em `CONSULTA_ACCESS_PASSWORD`.
4. Rode as migrations:

```bash
php artisan migrate
```

5. Inicie o servidor:

```bash
php artisan serve
```

## URLs

- Cadastro publico: `/cadastro`
- Login da consulta: `/cadastro/consulta/login`
- Consulta protegida: `/cadastro/consulta`

## Funcionalidades

- Cadastro com validacoes no backend
- Campos obrigatorios e opcionais conforme o requisito
- Campo `RA - Regiao Administrativa` no cadastro, consulta, filtro e exportacao
- Radio button para tipo de contato: `Visita` e `Ligacao`
- Consulta com filtros por codigo, nome, celular, lideranca, tipo, atualizado por e periodo
- Edicao de registros
- Exportacao XLSX respeitando os filtros aplicados
- Layout responsivo com Bootstrap
- Mascara para celular e CEP
- Preenchimento automatico de logradouro via ViaCEP quando o CEP for informado

## Publicacao no servidor

1. Aponte o virtual host/domino para a pasta `public`.
2. Configure o `.env` de producao com:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - credenciais reais do MySQL
   - `CONSULTA_ACCESS_PASSWORD` forte
3. Execute:

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
```

## Observacao sobre XLSX

A exportacao foi implementada sem dependencia externa para evitar bloqueio por extensoes ausentes no ambiente atual do PHP CLI. Caso queira futuramente trocar por uma biblioteca dedicada, basta substituir a classe `App\Support\SimpleXlsxExporter`.
