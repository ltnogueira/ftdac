<?php

namespace Tests\Feature;

use App\Models\Cadastro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CadastroFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_publico_pode_criar_um_cadastro(): void
    {
        $response = $this->post(route('cadastros.store'), [
            'nome' => 'Maria Silva',
            'apelido' => 'Mariá',
            'ra' => 'Ceilândia',
            'cep' => '01001-000',
            'logradouro' => 'Rua São João',
            'numero' => '123a',
            'complemento' => 'Casa fundo',
            'celular' => '(11) 91234-5678',
            'email' => 'mÁria@example.com',
            'lideranca' => 'Coordenadora Áurea',
        ]);

        $response->assertRedirect(route('cadastros.create'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cadastros', [
            'codigo' => '0001',
            'nome' => 'MARIA SILVA',
            'apelido' => 'MARIA',
            'ra' => 'CEILANDIA',
            'logradouro' => 'RUA SAO JOAO',
            'numero' => '123A',
            'complemento' => 'CASA FUNDO',
            'celular' => '11912345678',
            'email' => 'maria@example.com',
            'lideranca' => 'COORDENADORA AUREA',
            'atualizado_por' => Cadastro::DEFAULT_ATUALIZADO_POR,
            'tipo_contato' => Cadastro::DEFAULT_TIPO_CONTATO,
        ]);
    }

    public function test_apelido_email_lideranca_e_complemento_sao_opcionais_no_cadastro(): void
    {
        $response = $this->post(route('cadastros.store'), [
            'nome' => 'Joao Silva',
            'apelido' => '',
            'ra' => 'Gama',
            'cep' => '02002-000',
            'logradouro' => 'Rua B',
            'numero' => '200',
            'complemento' => '',
            'celular' => '(11) 99888-7777',
            'email' => '',
            'lideranca' => '',
        ]);

        $response->assertRedirect(route('cadastros.create'));
        $response->assertSessionDoesntHaveErrors([
            'apelido',
            'complemento',
            'email',
            'lideranca',
            'atualizado_por',
            'tipo_contato',
        ]);

        $this->assertDatabaseHas('cadastros', [
            'codigo' => '0001',
            'apelido' => null,
            'complemento' => null,
            'email' => null,
            'lideranca' => null,
            'atualizado_por' => Cadastro::DEFAULT_ATUALIZADO_POR,
            'tipo_contato' => Cadastro::DEFAULT_TIPO_CONTATO,
        ]);
    }

    public function test_edicao_preserva_codigo_e_campos_ocultos(): void
    {
        $cadastro = Cadastro::query()->create([
            'codigo' => '0007',
            'nome' => 'NOME ORIGINAL',
            'apelido' => 'ORIGINAL',
            'ra' => 'GAMA',
            'cep' => '01001000',
            'logradouro' => 'RUA ORIGINAL',
            'numero' => '10',
            'complemento' => null,
            'celular' => '11912345678',
            'email' => 'original@example.com',
            'lideranca' => 'LIDER ANTIGA',
            'atualizado_por' => 'OPERADOR ANTIGO',
            'tipo_contato' => Cadastro::TIPO_VISITA,
        ]);

        $this->withSession([config('consulta.session_key') => true])
            ->put(route('cadastros.update', $cadastro), [
                'nome' => 'Nome Atualizado',
                'apelido' => '',
                'ra' => 'Santa Maria',
                'cep' => '02002000',
                'logradouro' => 'Rua Atualizada',
                'numero' => '20',
                'complemento' => '',
                'celular' => '(11) 99888-7777',
                'email' => '',
            ])
            ->assertRedirect(route('cadastros.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('cadastros', [
            'id' => $cadastro->id,
            'codigo' => '0007',
            'nome' => 'NOME ATUALIZADO',
            'ra' => 'SANTA MARIA',
            'logradouro' => 'RUA ATUALIZADA',
            'lideranca' => 'LIDER ANTIGA',
            'atualizado_por' => 'OPERADOR ANTIGO',
            'tipo_contato' => Cadastro::TIPO_VISITA,
        ]);
    }

    public function test_codigo_automatico_ignora_codigos_legados_fora_do_padrao_quatro_digitos(): void
    {
        Cadastro::query()->create([
            'codigo' => '88435',
            'nome' => 'LEGADO',
            'apelido' => null,
            'ra' => 'GAMA',
            'cep' => '01001000',
            'logradouro' => 'RUA LEGADA',
            'numero' => '1',
            'complemento' => null,
            'celular' => '11912345678',
            'email' => null,
            'lideranca' => null,
            'atualizado_por' => 'ADMIN',
            'tipo_contato' => Cadastro::TIPO_LIGACAO,
        ]);

        $response = $this->post(route('cadastros.store'), [
            'nome' => 'Novo Cadastro',
            'apelido' => '',
            'ra' => 'Santa Maria',
            'cep' => '02002000',
            'logradouro' => 'Rua Nova',
            'numero' => '20',
            'complemento' => '',
            'celular' => '(11) 99888-7777',
            'email' => '',
        ]);

        $response->assertRedirect(route('cadastros.create'));

        $this->assertDatabaseHas('cadastros', [
            'nome' => 'NOVO CADASTRO',
            'codigo' => '0001',
        ]);
    }

    public function test_consulta_exige_autenticacao_simples(): void
    {
        $this->get(route('cadastros.index'))
            ->assertRedirect(route('consulta.login'));

        $this->post(route('consulta.authenticate'), [
            'senha' => config('consulta.password'),
        ])->assertRedirect(route('cadastros.index'));

        $this->get(route('cadastros.index'))
            ->assertOk()
            ->assertSee('Consulta de cadastros');
    }

    public function test_exportacao_respeita_filtros_aplicados(): void
    {
        $visita = Cadastro::query()->create([
            'codigo' => 'VIS001',
            'nome' => 'CADASTRO VISITA',
            'apelido' => 'VISITA',
            'ra' => 'TAGUATINGA',
            'cep' => '01001000',
            'logradouro' => 'RUA TESTE',
            'numero' => '10',
            'complemento' => 'CASA',
            'celular' => '11912345678',
            'email' => 'visita@example.com',
            'lideranca' => 'REGIONAL A',
            'atualizado_por' => 'ADMIN',
            'tipo_contato' => Cadastro::TIPO_VISITA,
        ]);

        $ligacao = Cadastro::query()->create([
            'codigo' => 'LIG001',
            'nome' => 'CADASTRO LIGACAO',
            'apelido' => 'LIGACAO',
            'ra' => 'GAMA',
            'cep' => '02002000',
            'logradouro' => 'AVENIDA TESTE',
            'numero' => '20',
            'complemento' => 'APTO 1',
            'celular' => '11987654321',
            'email' => 'ligacao@example.com',
            'lideranca' => 'REGIONAL B',
            'atualizado_por' => 'ADMIN',
            'tipo_contato' => Cadastro::TIPO_LIGACAO,
        ]);

        $filtrados = Cadastro::query()
            ->filtrar([
                'tipo_contato' => Cadastro::TIPO_VISITA,
                'ra' => 'Taguatinga',
            ])
            ->pluck('id')
            ->all();

        $this->assertSame([$visita->id], $filtrados);
        $this->assertNotContains($ligacao->id, $filtrados);

        $response = $this->withSession([config('consulta.session_key') => true])
            ->get(route('cadastros.export', ['tipo_contato' => Cadastro::TIPO_VISITA, 'ra' => 'Taguatinga']))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $content = $response->getContent();

        $this->assertStringContainsString('Logradouro', $content);
        $this->assertStringContainsString('RUA TESTE', $content);
        $this->assertStringNotContainsString('AVENIDA TESTE', $content);
    }

    public function test_consulta_permite_excluir_um_cadastro(): void
    {
        $cadastro = Cadastro::query()->create([
            'codigo' => 'DEL001',
            'nome' => 'CADASTRO EXCLUIR',
            'apelido' => 'EXCLUIR',
            'ra' => 'PLANALTINA',
            'cep' => '73000000',
            'logradouro' => 'RUA A',
            'numero' => '100',
            'complemento' => 'CASA',
            'celular' => '11999999999',
            'email' => 'excluir@example.com',
            'lideranca' => 'REGIONAL C',
            'atualizado_por' => 'ADMIN',
            'tipo_contato' => Cadastro::TIPO_VISITA,
        ]);

        $this->withSession([config('consulta.session_key') => true])
            ->delete(route('cadastros.destroy', $cadastro))
            ->assertRedirect(route('cadastros.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('cadastros', [
            'id' => $cadastro->id,
        ]);
    }
}
