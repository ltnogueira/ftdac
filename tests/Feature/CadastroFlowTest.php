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
            'codigo' => 'cod001',
            'nome' => 'Maria Silva',
            'apelido' => 'Mariá',
            'ra' => 'Ceilândia',
            'cep' => '01001-000',
            'logradouro' => 'Rua São João',
            'numero' => '123a',
            'complemento' => 'Casa fundo',
            'celular' => '(11) 91234-5678',
            'email' => 'mÁria@example.com',
            'lideranca' => 'Regional Á',
            'atualizado_por' => 'Equipe Cadastro',
            'tipo_contato' => Cadastro::TIPO_VISITA,
        ]);

        $response->assertRedirect(route('cadastros.create'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cadastros', [
            'codigo' => 'COD001',
            'nome' => 'MARIA SILVA',
            'apelido' => 'MARIA',
            'ra' => 'CEILANDIA',
            'logradouro' => 'RUA SAO JOAO',
            'numero' => '123A',
            'complemento' => 'CASA FUNDO',
            'celular' => '11912345678',
            'email' => 'maria@example.com',
            'lideranca' => 'REGIONAL A',
            'atualizado_por' => 'EQUIPE CADASTRO',
            'tipo_contato' => Cadastro::TIPO_VISITA,
        ]);
    }

    public function test_apelido_email_lideranca_e_complemento_sao_opcionais_no_cadastro(): void
    {
        $response = $this->post(route('cadastros.store'), [
            'codigo' => 'cod002',
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
            'atualizado_por' => 'Operador',
            'tipo_contato' => Cadastro::TIPO_LIGACAO,
        ]);

        $response->assertRedirect(route('cadastros.create'));
        $response->assertSessionDoesntHaveErrors([
            'apelido',
            'complemento',
            'email',
            'lideranca',
        ]);

        $this->assertDatabaseHas('cadastros', [
            'codigo' => 'COD002',
            'apelido' => null,
            'complemento' => null,
            'email' => null,
            'lideranca' => null,
            'atualizado_por' => 'OPERADOR',
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
