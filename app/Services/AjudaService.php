<?php

namespace App\Services;

use App\Utils\RejeicoesSefazUtil;
use Illuminate\Support\Str;

class AjudaService
{
    public function categorias(): array
    {
        return [
            [
                'slug' => 'primeiros-passos',
                'titulo' => 'Primeiros Passos',
                'descricao' => 'Configure sua empresa antes de emitir documentos fiscais',
                'icone' => 'fas fa-play',
                'cor' => 'bg-primary-soft',
            ],
            [
                'slug' => 'clientes',
                'titulo' => 'Clientes',
                'descricao' => 'Cadastro de quem vai receber suas notas e cupons',
                'icone' => 'fas fa-user',
                'cor' => 'bg-info-soft',
            ],
            [
                'slug' => 'produtos-estoque',
                'titulo' => 'Produtos e Estoque',
                'descricao' => 'Cadastro de produtos e controle de níveis de estoque',
                'icone' => 'fas fa-box-open',
                'cor' => 'bg-warning-soft',
            ],
            [
                'slug' => 'nfe',
                'titulo' => 'NFe',
                'descricao' => 'Emissão de nota fiscal eletrônica',
                'icone' => 'far fa-file-alt',
                'cor' => 'bg-success-soft',
            ],
            [
                'slug' => 'pdv-nfce',
                'titulo' => 'PDV / NFCe',
                'descricao' => 'Cupom fiscal para vendas de balcão',
                'icone' => 'fas fa-cash-register',
                'cor' => 'bg-danger-soft',
            ],
            [
                'slug' => 'mdfe',
                'titulo' => 'MDFe',
                'descricao' => 'Manifesto eletrônico de documentos fiscais',
                'icone' => 'fas fa-truck',
                'cor' => 'bg-primary-soft',
            ],
            [
                'slug' => 'financeiro',
                'titulo' => 'Financeiro',
                'descricao' => 'Fluxo de caixa, DRE e faturas da assinatura',
                'icone' => 'fas fa-wallet',
                'cor' => 'bg-info-soft',
            ],
            [
                'slug' => 'rejeicoes-sefaz',
                'titulo' => 'Códigos de Rejeição SEFAZ',
                'descricao' => 'O que significa e como resolver cada rejeição de NFe/NFCe',
                'icone' => 'fas fa-exclamation-triangle',
                'cor' => 'bg-danger-soft',
            ],
        ];
    }

    public function categoriaPorSlug(string $slug): ?array
    {
        foreach ($this->categorias() as $categoria) {
            if ($categoria['slug'] === $slug) {
                return $categoria;
            }
        }

        return null;
    }

    public function artigos(): array
    {
        return array_merge($this->artigosGerais(), $this->artigosRejeicoesSefaz());
    }

    public function artigosPorCategoria(string $categoriaSlug): array
    {
        return array_values(array_filter($this->artigos(), function ($artigo) use ($categoriaSlug) {
            return $artigo['categoria'] === $categoriaSlug;
        }));
    }

    public function artigoPorSlug(string $slug): ?array
    {
        foreach ($this->artigos() as $artigo) {
            if ($artigo['slug'] === $slug) {
                return $artigo;
            }
        }

        return null;
    }

    public function buscar(string $termo): array
    {
        $termo = $this->normalizar($termo);

        if ($termo === '') {
            return [];
        }

        return array_values(array_filter($this->artigos(), function ($artigo) use ($termo) {
            $textoBusca = $this->normalizar($artigo['titulo'].' '.$artigo['resumo'].' '.($artigo['palavras_chave'] ?? ''));

            return str_contains($textoBusca, $termo);
        }));
    }

    /**
     * Remove acentos, pontuação e caixa alta para permitir buscas como
     * "certificado" encontrar "Certificado Digital (.pfx)" ou "nao" encontrar "não".
     */
    private function normalizar(string $texto): string
    {
        $texto = Str::ascii($texto);
        $texto = mb_strtolower($texto);
        $texto = preg_replace('/[^a-z0-9]+/', ' ', $texto);

        return trim($texto);
    }

    public function artigosPopulares(): array
    {
        $slugs = ['primeiros-passos', 'emitir-nfe', 'rejeicao-204', 'rejeicao-539', 'produtos-estoque'];

        return array_values(array_filter(array_map(fn ($slug) => $this->artigoPorSlug($slug), $slugs)));
    }

    private function artigosGerais(): array
    {
        return [
            [
                'slug' => 'primeiros-passos',
                'categoria' => 'primeiros-passos',
                'titulo' => 'Primeiros passos no sistema',
                'resumo' => 'Configure a sua empresa antes de emitir documentos fiscais.',
                'palavras_chave' => 'empresa certificado digital pfx senha configuracoes usuarios formas de pagamento cadastro inicial',
                'blocos' => [
                    ['paragrafo' => 'Antes de emitir a primeira nota, é preciso deixar o cadastro da sua empresa completo. São só alguns passos.'],
                    ['heading' => 'Passo a passo', 'lista' => [
                        'Em <b>Administração &gt; Configurações</b>, preencha os dados da empresa (razão social, CNPJ, endereço e regime tributário).',
                        'Ainda em Configurações, envie o <b>Certificado Digital (.pfx)</b> e informe a senha — ele é obrigatório para emitir NFe, NFCe e MDFe.',
                        'Cadastre as <b>Formas de Pagamento</b> que sua empresa utiliza.',
                        'Cadastre os <b>Usuários</b> da equipe e defina o perfil de acesso de cada um.',
                    ]],
                ],
                'cta_label' => 'Ir para Configurações',
                'cta_url' => route('empresa.index'),
            ],
            [
                'slug' => 'cadastrar-cliente',
                'categoria' => 'clientes',
                'titulo' => 'Como cadastrar um cliente',
                'resumo' => 'Cadastre quem vai receber suas notas e cupons.',
                'palavras_chave' => 'cliente cadastro cpf cnpj endereco destinatario',
                'blocos' => [
                    ['heading' => 'Passo a passo', 'lista' => [
                        'Acesse <b>Cliente</b> no menu e clique em <b>Novo Cliente</b>.',
                        'Informe o CPF ou CNPJ — o sistema busca automaticamente os dados cadastrais.',
                        'Complete o endereço e salve o cadastro.',
                        'Use a busca da listagem para localizar, visualizar ou editar um cliente já cadastrado.',
                    ]],
                ],
                'cta_label' => 'Ir para Clientes',
                'cta_url' => route('cliente.index'),
            ],
            [
                'slug' => 'produtos-estoque',
                'categoria' => 'produtos-estoque',
                'titulo' => 'Cadastro de produtos e controle de estoque',
                'resumo' => 'Cadastre produtos e acompanhe o que está disponível.',
                'palavras_chave' => 'produto estoque ncm cfop categoria entrada nfe importar xml disponivel sem estoque chassi',
                'blocos' => [
                    ['heading' => 'Passo a passo', 'lista' => [
                        'Em <b>Produtos &gt; Novo Produto</b>, preencha código, NCM, CFOP e os preços de custo e venda.',
                        'Organize os produtos em <b>Categorias</b> para facilitar filtros e relatórios.',
                        'Ao importar uma nota de compra em <b>Entradas NFE &gt; Importar NFe</b>, o estoque dos produtos é atualizado automaticamente.',
                        'Acompanhe os níveis em <b>Estoque</b>: os cards do topo mostram quantos produtos estão disponíveis e quantos estão sem estoque.',
                    ]],
                ],
                'cta_label' => 'Ir para Produtos',
                'cta_url' => route('produto.index'),
            ],
            [
                'slug' => 'emitir-nfe',
                'categoria' => 'nfe',
                'titulo' => 'Como emitir uma NFe',
                'resumo' => 'Nota fiscal eletrônica para vendas e remessas.',
                'palavras_chave' => 'nfe nota fiscal eletronica emitir danfe xml venda',
                'blocos' => [
                    ['heading' => 'Passo a passo', 'lista' => [
                        'Acesse <b>NFe &gt; Emitir NFe</b>.',
                        'Selecione o cliente e adicione os produtos com as quantidades vendidas.',
                        'Revise os impostos calculados e confirme a emissão.',
                        'Depois de autorizada, baixe o XML e o DANFE em <b>NFe &gt; Notas Emitidas</b> ou <b>Baixar XML</b>.',
                    ]],
                ],
                'cta_label' => 'Emitir NFe',
                'cta_url' => '/vendas/nova',
            ],
            [
                'slug' => 'emitir-nfce',
                'categoria' => 'pdv-nfce',
                'titulo' => 'Como emitir uma NFCe pelo PDV',
                'resumo' => 'Cupom fiscal para vendas de balcão.',
                'palavras_chave' => 'pdv nfce cupom fiscal balcao venda rapida',
                'blocos' => [
                    ['heading' => 'Passo a passo', 'lista' => [
                        'Acesse <b>PDV &gt; Emitir NFCe</b>.',
                        'Adicione os produtos vendidos e a forma de pagamento.',
                        'Confirme a emissão do cupom.',
                        'Consulte os cupons emitidos e baixe os XMLs em <b>PDV &gt; Notas Emitidas</b> ou <b>Baixar XML</b>.',
                    ]],
                ],
                'cta_label' => 'Emitir NFCe',
                'cta_url' => route('cupom.create'),
            ],
            [
                'slug' => 'emitir-mdfe',
                'categoria' => 'mdfe',
                'titulo' => 'Como emitir um MDFe',
                'resumo' => 'Manifesto eletrônico de documentos fiscais.',
                'palavras_chave' => 'mdfe manifesto veiculo motorista carga transporte',
                'blocos' => [
                    ['heading' => 'Passo a passo', 'lista' => [
                        'Cadastre os <b>Veículos</b> e <b>Motoristas</b> em MDFe.',
                        'Acesse <b>MDFe &gt; Emitir MDFe</b> e vincule as notas fiscais que compõem a carga.',
                        'Confirme a emissão do manifesto.',
                        'Acompanhe os manifestos emitidos e os relatórios em <b>MDFe &gt; Relatórios</b>.',
                    ]],
                ],
                'cta_label' => 'Emitir MDFe',
                'cta_url' => route('mdfe.create'),
            ],
            [
                'slug' => 'fluxo-de-caixa',
                'categoria' => 'financeiro',
                'titulo' => 'Fluxo de caixa e financeiro',
                'resumo' => 'Acompanhe entradas, saídas e faturas.',
                'palavras_chave' => 'financeiro fluxo de caixa dre fatura assinatura plano pagamento',
                'blocos' => [
                    ['heading' => 'Passo a passo', 'lista' => [
                        'Vendas de NFe e NFCe entram automaticamente no <b>Fluxo de Caixa</b>.',
                        'Lance entradas e saídas manuais quando necessário.',
                        'Consulte relatórios e o DRE em <b>Administração &gt; Fluxo de Caixa</b>.',
                        'Acompanhe a assinatura do plano e pague faturas em <b>Administração &gt; Faturas</b>.',
                    ]],
                ],
                'cta_label' => 'Ir para Fluxo de Caixa',
                'cta_url' => route('fluxo-caixa.index'),
            ],
        ];
    }

    /**
     * Um artigo por código de rejeição importado da tabela MOC 7.00.
     */
    private function artigosRejeicoesSefaz(): array
    {
        return array_map(
            fn ($item) => RejeicoesSefazUtil::artigoAjuda($item),
            RejeicoesSefazUtil::todas()
        );
    }
}
