<?php

namespace Database\Seeders;

use App\Models\CClassTrib;
use App\Models\CstIbsCbs;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RtcCompletoSeeder extends Seeder
{
    public function run()
    {
        // ==========================================
        // 1. POPULAR CSTs (Tabela cst_ibs_cbs)
        // ==========================================
        $csts = [
            ['codigo' => '000', 'descricao' => 'Tributação integral'], // CSV: 0 -> Ajustado para 000
            ['codigo' => '010', 'descricao' => 'Tributação com alíquotas uniformes'], // CSV: 10 -> 010
            ['codigo' => '011', 'descricao' => 'Tributação com alíquotas uniformes reduzidas'], // CSV: 11 -> 011
            ['codigo' => '200', 'descricao' => 'Alíquota reduzida'],
            ['codigo' => '220', 'descricao' => 'Alíquota fixa'],
            ['codigo' => '221', 'descricao' => 'Alíquota fixa proporcional'],
            ['codigo' => '222', 'descricao' => 'Redução de base de cálculo'],
            ['codigo' => '400', 'descricao' => 'Isenção'],
            ['codigo' => '410', 'descricao' => 'Imunidade e não incidência'],
            ['codigo' => '510', 'descricao' => 'Diferimento'],
            ['codigo' => '515', 'descricao' => 'Diferimento com redução de alíquota'],
            ['codigo' => '550', 'descricao' => 'Suspensão'],
            ['codigo' => '620', 'descricao' => 'Tributação monofásica'],
            ['codigo' => '800', 'descricao' => 'Transferência de crédito'],
            ['codigo' => '810', 'descricao' => 'Ajuste de IBS na ZFM'],
            ['codigo' => '811', 'descricao' => 'Ajustes'],
            ['codigo' => '820', 'descricao' => 'Tributação em declaração de regime específico'],
            ['codigo' => '830', 'descricao' => 'Exclusão de base de cálculo'],
        ];

        foreach ($csts as $cst) {
            CstIbsCbs::updateOrCreate(['codigo' => $cst['codigo']], $cst);
        }

        // ==========================================
        // 2. POPULAR cClassTrib (Tabela c_class_tribs)
        // ==========================================
        
        // Array gigante extraído do seu CSV. 
        // Lógica: 'codigo' => Coluna C, 'descricao' => Coluna D (Nome) + E (Desc), 'cst' => Coluna A
        $classificacoes = [
            // CST 000
            ['cst' => '000', 'codigo' => '000001', 'descricao' => 'Situações tributadas integralmente pelo IBS e CBS.'],
            ['cst' => '000', 'codigo' => '000002', 'descricao' => 'Exploração de via, observado o art. 11 da LC 214/2025.'],
            ['cst' => '000', 'codigo' => '000003', 'descricao' => 'Regime automotivo - projetos incentivados (art. 311)'],
            ['cst' => '000', 'codigo' => '000004', 'descricao' => 'Regime automotivo - projetos incentivados (art. 312)'],

            // CST 010
            ['cst' => '010', 'codigo' => '010001', 'descricao' => 'Operações do FGTS não realizadas pela Caixa Econômica Federal'],
            ['cst' => '010', 'codigo' => '010002', 'descricao' => 'Operações do serviço financeiro'],

            // CST 011
            ['cst' => '011', 'codigo' => '011001', 'descricao' => 'Planos de assistência funerária (Redução 60%)'],
            ['cst' => '011', 'codigo' => '011002', 'descricao' => 'Planos de assistência à saúde (Redução 60%)'],
            ['cst' => '011', 'codigo' => '011003', 'descricao' => 'Intermediação de planos de assistência à saúde (Redução 60%)'],
            ['cst' => '011', 'codigo' => '011004', 'descricao' => 'Concursos e prognósticos'],
            ['cst' => '011', 'codigo' => '011005', 'descricao' => 'Planos de assistência à saúde de animais domésticos (Redução 30%)'],

            // CST 200 (Alíquota Reduzida / Zero)
            ['cst' => '200', 'codigo' => '200001', 'descricao' => 'Aquisições realizadas entre empresas autorizadas a operar em ZPE (Alíquota Zero)'],
            ['cst' => '200', 'codigo' => '200002', 'descricao' => 'Fornecimento/importação de máquinas p/ produtor rural não contribuinte (Alíquota Zero)'],
            ['cst' => '200', 'codigo' => '200003', 'descricao' => 'Cesta Básica Nacional - Alimentos (Anexo I) (Alíquota Zero)'],
            ['cst' => '200', 'codigo' => '200004', 'descricao' => 'Dispositivos médicos (Anexo XII) (Alíquota Zero)'],
            ['cst' => '200', 'codigo' => '200005', 'descricao' => 'Dispositivos médicos adquiridos por órgãos públicos (Anexo IV) (Alíquota Zero)'],
            ['cst' => '200', 'codigo' => '200006', 'descricao' => 'Situação de emergência de saúde pública (Anexo XII)'],
            ['cst' => '200', 'codigo' => '200007', 'descricao' => 'Dispositivos de acessibilidade p/ PCD (Anexo XIII) (Alíquota Zero)'],
            ['cst' => '200', 'codigo' => '200008', 'descricao' => 'Dispositivos de acessibilidade p/ PCD órgãos públicos (Anexo V) (Alíquota Zero)'],
            ['cst' => '200', 'codigo' => '200009', 'descricao' => 'Medicamentos (Anexo XIV) (Alíquota Zero)'],
            ['cst' => '200', 'codigo' => '200010', 'descricao' => 'Medicamentos Anvisa adquiridos por órgãos públicos (Alíquota Zero)'],
            ['cst' => '200', 'codigo' => '200011', 'descricao' => 'Nutrição enteral/parenteral órgãos públicos (Anexo VI) (Alíquota Zero)'],
            ['cst' => '200', 'codigo' => '200012', 'descricao' => 'Medicamentos em emergência de saúde pública (Anexo XIV)'],
            ['cst' => '200', 'codigo' => '200013', 'descricao' => 'Produtos de saúde menstrual (Absorventes, etc) (Alíquota Zero)'],
            ['cst' => '200', 'codigo' => '200014', 'descricao' => 'Hortícolas, frutas e ovos (Anexo XV) (Alíquota Zero)'],
            ['cst' => '200', 'codigo' => '200015', 'descricao' => 'Automóveis passageiros p/ motoristas profissionais ou PCD (Alíquota Zero)'],
            ['cst' => '200', 'codigo' => '200016', 'descricao' => 'Serviços de P&D por ICT sem fins lucrativos (Alíquota Zero)'],
            ['cst' => '200', 'codigo' => '200017', 'descricao' => 'Operações relacionadas ao FGTS (Alíquota Zero)'],
            ['cst' => '200', 'codigo' => '200018', 'descricao' => 'Operações de resseguro e retrocessão (Alíquota Zero)'],
            ['cst' => '200', 'codigo' => '200019', 'descricao' => 'Importador de serviços financeiros contribuinte (Alíquota Zero)'],
            ['cst' => '200', 'codigo' => '200020', 'descricao' => 'Operação cooperativas regime específico (Alíquota Zero)'],
            ['cst' => '200', 'codigo' => '200021', 'descricao' => 'Transporte público coletivo ferroviário e hidroviário (Alíquota Zero)'],
            ['cst' => '200', 'codigo' => '200022', 'descricao' => 'Bem industrializado de origem nacional destinado à ZFM (Alíquota Zero)'],
            ['cst' => '200', 'codigo' => '200023', 'descricao' => 'Bem intermediário entre indústrias incentivadas na ZFM (Alíquota Zero)'],
            ['cst' => '200', 'codigo' => '200024', 'descricao' => 'Bem industrializado destinado a Áreas de Livre Comércio (Alíquota Zero)'],
            ['cst' => '200', 'codigo' => '200025', 'descricao' => 'Serviços de educação Prouni (Zero CBS / Reduzida IBS 60%)'],
            ['cst' => '200', 'codigo' => '200026', 'descricao' => 'Locação de imóveis em zonas reabilitadas (Redução 80%)'],
            ['cst' => '200', 'codigo' => '200027', 'descricao' => 'Locação, cessão e arrendamento de imóveis (Redução 70%)'],
            ['cst' => '200', 'codigo' => '200028', 'descricao' => 'Serviços de educação (Anexo II) (Redução 60%)'],
            ['cst' => '200', 'codigo' => '200029', 'descricao' => 'Serviços de saúde humana (Anexo III) (Redução 60%)'],
            ['cst' => '200', 'codigo' => '200030', 'descricao' => 'Venda de dispositivos médicos (Anexo IV) (Redução 60%)'],
            ['cst' => '200', 'codigo' => '200031', 'descricao' => 'Dispositivos de acessibilidade PCD (Anexo V) (Redução 60%)'],
            ['cst' => '200', 'codigo' => '200032', 'descricao' => 'Medicamentos Anvisa (Exceto Zero) (Redução 60%)'],
            ['cst' => '200', 'codigo' => '200033', 'descricao' => 'Nutrição enteral e parenteral (Anexo VI) (Redução 60%)'],
            ['cst' => '200', 'codigo' => '200034', 'descricao' => 'Alimentos consumo humano (Anexo VII) (Redução 60%)'],
            ['cst' => '200', 'codigo' => '200035', 'descricao' => 'Produtos de higiene e limpeza (Anexo VIII) (Redução 60%)'],
            ['cst' => '200', 'codigo' => '200036', 'descricao' => 'Produtos agropecuários in natura (Redução 60%)'],
            ['cst' => '200', 'codigo' => '200037', 'descricao' => 'Serviços ambientais e vegetação nativa (Redução 60%)'],
            ['cst' => '200', 'codigo' => '200038', 'descricao' => 'Insumos agropecuários e aquícolas (Anexo IX) (Redução 60%)'],
            ['cst' => '200', 'codigo' => '200039', 'descricao' => 'Produções nacionais artísticas e culturais (Anexo X) (Redução 60%)'],
            ['cst' => '200', 'codigo' => '200040', 'descricao' => 'Comunicação institucional à administração pública (Redução 60%)'],
            ['cst' => '200', 'codigo' => '200041', 'descricao' => 'Serviço de educação desportiva (Redução 60%)'],
            ['cst' => '200', 'codigo' => '200042', 'descricao' => 'Gestão e exploração do desporto (Redução 60%)'],
            ['cst' => '200', 'codigo' => '200043', 'descricao' => 'Bens de soberania e segurança nacional (Anexo XI) (Redução 60%)'],
            ['cst' => '200', 'codigo' => '200044', 'descricao' => 'Segurança cibernética (Sócio BR) (Anexo XI) (Redução 60%)'],
            ['cst' => '200', 'codigo' => '200045', 'descricao' => 'Projetos de reabilitação urbana (Redução 60%)'],
            ['cst' => '200', 'codigo' => '200046', 'descricao' => 'Operações com bens imóveis (Redução 50%)'],
            ['cst' => '200', 'codigo' => '200047', 'descricao' => 'Bares e Restaurantes (Redução 40%)'],
            ['cst' => '200', 'codigo' => '200048', 'descricao' => 'Hotelaria e Parques (Redução 40%)'],
            ['cst' => '200', 'codigo' => '200049', 'descricao' => 'Transporte coletivo rodoviário/ferroviário intermunicipal (Redução 40%)'],
            ['cst' => '200', 'codigo' => '200050', 'descricao' => 'Transporte aéreo regional (Redução 40%)'],
            ['cst' => '200', 'codigo' => '200051', 'descricao' => 'Agências de Turismo (Redução 40%)'],
            ['cst' => '200', 'codigo' => '200052', 'descricao' => 'Profissões intelectuais (Advogados, Eng, Médicos, etc) (Redução 30%)'],

            // CST 220 (Alíquota Fixa)
            ['cst' => '220', 'codigo' => '220001', 'descricao' => 'Incorporação imobiliária (Regime Especial - 2,08%)'],
            ['cst' => '220', 'codigo' => '220002', 'descricao' => 'Incorporação imobiliária (Regime Especial - 0,53%)'],
            ['cst' => '220', 'codigo' => '220003', 'descricao' => 'Alienação de imóvel parcelamento do solo (3,65%)'],

            // CST 221
            ['cst' => '221', 'codigo' => '221001', 'descricao' => 'Locação de imóvel com alíquota sobre receita bruta (3,65%)'],

            // CST 222
            ['cst' => '222', 'codigo' => '222001', 'descricao' => 'Transporte internacional de passageiros (Redução Base Cálculo)'],

            // CST 400 (Isenção)
            ['cst' => '400', 'codigo' => '400001', 'descricao' => 'Transporte público coletivo urbano/metropolitano'],

            // CST 410 (Imunidade / Não Incidência)
            ['cst' => '410', 'codigo' => '410001', 'descricao' => 'Bonificações em documento fiscal (Não incidência)'],
            ['cst' => '410', 'codigo' => '410002', 'descricao' => 'Transferência entre estabelecimentos mesmo contribuinte'],
            ['cst' => '410', 'codigo' => '410003', 'descricao' => 'Doações sem contraprestação'],
            ['cst' => '410', 'codigo' => '410004', 'descricao' => 'Exportações de bens e serviços (Imunidade)'],
            ['cst' => '410', 'codigo' => '410005', 'descricao' => 'Fornecimentos pela União, Estados e Municípios (Imunidade Recíproca)'],
            ['cst' => '410', 'codigo' => '410006', 'descricao' => 'Entidades religiosas e templos (Imunidade)'],
            ['cst' => '410', 'codigo' => '410007', 'descricao' => 'Partidos políticos e sindicatos (Imunidade)'],
            ['cst' => '410', 'codigo' => '410008', 'descricao' => 'Livros, jornais, periódicos e papel (Imunidade)'],
            ['cst' => '410', 'codigo' => '410009', 'descricao' => 'Fonogramas e videofonogramas musicais brasileiros (Imunidade)'],
            ['cst' => '410', 'codigo' => '410010', 'descricao' => 'Radiodifusão sonora e de sons e imagens gratuita'],
            ['cst' => '410', 'codigo' => '410011', 'descricao' => 'Ouro como ativo financeiro'],
            ['cst' => '410', 'codigo' => '410012', 'descricao' => 'Condomínio edilício não optante pelo regime regular'],
            ['cst' => '410', 'codigo' => '410013', 'descricao' => 'Exportações de combustíveis'],
            ['cst' => '410', 'codigo' => '410014', 'descricao' => 'Produtor rural não contribuinte'],
            ['cst' => '410', 'codigo' => '410015', 'descricao' => 'Transportador autônomo não contribuinte'],
            ['cst' => '410', 'codigo' => '410016', 'descricao' => 'Resíduos sólidos'],
            ['cst' => '410', 'codigo' => '410017', 'descricao' => 'Bem móvel usado para revenda (Crédito Presumido)'],
            ['cst' => '410', 'codigo' => '410018', 'descricao' => 'Fundos garantidores e políticas públicas'],
            ['cst' => '410', 'codigo' => '410019', 'descricao' => 'Exclusão da gorjeta da base de cálculo'],
            ['cst' => '410', 'codigo' => '410020', 'descricao' => 'Exclusão taxa intermediação delivery (Bares/Restaurantes)'],
            ['cst' => '410', 'codigo' => '410021', 'descricao' => 'Contribuição iluminação pública (Art 149-A CF)'],
            ['cst' => '410', 'codigo' => '410022', 'descricao' => 'Consolidação propriedade pelo credor (Garantia)'],
            ['cst' => '410', 'codigo' => '410023', 'descricao' => 'Alienação de garantia (Prestador não contribuinte)'],
            ['cst' => '410', 'codigo' => '410024', 'descricao' => 'Consolidação propriedade consórcio'],
            ['cst' => '410', 'codigo' => '410025', 'descricao' => 'Alienação garantia consórcio'],
            ['cst' => '410', 'codigo' => '410026', 'descricao' => 'Doação com anulação de crédito'],
            ['cst' => '410', 'codigo' => '410027', 'descricao' => 'Exportação de serviço ou bem imaterial'],
            ['cst' => '410', 'codigo' => '410028', 'descricao' => 'Imóveis pessoa física não contribuinte'],
            ['cst' => '410', 'codigo' => '410029', 'descricao' => 'Operações acobertadas somente pelo ICMS'],
            ['cst' => '410', 'codigo' => '410030', 'descricao' => 'Estorno de crédito (Roubo, furto, extravio)'],
            ['cst' => '410', 'codigo' => '410031', 'descricao' => 'Fornecimento anterior à vigência'],
            ['cst' => '410', 'codigo' => '410999', 'descricao' => 'Operações não onerosas sem previsão de tributação'],

            // CST 510 (Diferimento)
            ['cst' => '510', 'codigo' => '510001', 'descricao' => 'Energia elétrica (Geração, transmissão, distribuição)'],

            // CST 515
            ['cst' => '515', 'codigo' => '515001', 'descricao' => 'Insumos agropecuários p/ produtor não contribuinte (Diferimento)'],

            // CST 550 (Suspensão)
            ['cst' => '550', 'codigo' => '550001', 'descricao' => 'Exportações de bens materiais (Fim específico)'],
            ['cst' => '550', 'codigo' => '550002', 'descricao' => 'Regime de Trânsito Aduaneiro'],
            ['cst' => '550', 'codigo' => '550003', 'descricao' => 'Regimes de Depósito'],
            ['cst' => '550', 'codigo' => '550004', 'descricao' => 'Loja Franca (Duty Free)'],
            ['cst' => '550', 'codigo' => '550005', 'descricao' => 'Consumo de bordo (Aeronaves/Navios)'],
            ['cst' => '550', 'codigo' => '550006', 'descricao' => 'Admissão Temporária'],
            ['cst' => '550', 'codigo' => '550007', 'descricao' => 'Regimes de Aperfeiçoamento (Drawback suspenção)'],
            ['cst' => '550', 'codigo' => '550008', 'descricao' => 'Repetro-Temporário'],
            ['cst' => '550', 'codigo' => '550009', 'descricao' => 'GNL-Temporário'],
            ['cst' => '550', 'codigo' => '550010', 'descricao' => 'Repetro-Permanente'],
            ['cst' => '550', 'codigo' => '550011', 'descricao' => 'Repetro-Industrialização'],
            ['cst' => '550', 'codigo' => '550012', 'descricao' => 'Repetro-Nacional'],
            ['cst' => '550', 'codigo' => '550013', 'descricao' => 'Repetro-Entreposto'],
            ['cst' => '550', 'codigo' => '550014', 'descricao' => 'ZPE (Zona Processamento Exportação)'],
            ['cst' => '550', 'codigo' => '550015', 'descricao' => 'Reporto (Portos)'],
            ['cst' => '550', 'codigo' => '550016', 'descricao' => 'Reidi (Infraestrutura)'],
            ['cst' => '550', 'codigo' => '550017', 'descricao' => 'Renaval (Naval)'],
            ['cst' => '550', 'codigo' => '550018', 'descricao' => 'Bens de Capital (Desoneração)'],
            ['cst' => '550', 'codigo' => '550019', 'descricao' => 'Importação indústria incentivada ZFM'],
            ['cst' => '550', 'codigo' => '550020', 'descricao' => 'Áreas de Livre Comércio'],
            ['cst' => '550', 'codigo' => '550021', 'descricao' => 'Industrialização destinada a exportações'],

            // CST 620 (Monofásico - Combustíveis)
            ['cst' => '620', 'codigo' => '620001', 'descricao' => 'Tributação monofásica sobre combustíveis'],
            ['cst' => '620', 'codigo' => '620002', 'descricao' => 'Monofásica com retenção (Refinaria/Importador)'],
            ['cst' => '620', 'codigo' => '620003', 'descricao' => 'Monofásica com tributos retidos'],
            ['cst' => '620', 'codigo' => '620004', 'descricao' => 'Monofásica mistura EAC com gasolina (Superior ao obrigatório)'],
            ['cst' => '620', 'codigo' => '620005', 'descricao' => 'Monofásica mistura EAC com gasolina (Inferior ao obrigatório)'],
            ['cst' => '620', 'codigo' => '620006', 'descricao' => 'Monofásica cobrada anteriormente'],

            // CST 800 (Transferência)
            ['cst' => '800', 'codigo' => '800001', 'descricao' => 'Fusão, cisão ou incorporação'],
            ['cst' => '800', 'codigo' => '800002', 'descricao' => 'Transferência crédito associado/cooperativa'],

            // CST 810
            ['cst' => '810', 'codigo' => '810001', 'descricao' => 'Crédito presumido IBS fornecimentos ZFM'],

            // CST 811
            ['cst' => '811', 'codigo' => '811001', 'descricao' => 'Anulação de Crédito por Saídas Imunes/Isentas'],
            ['cst' => '811', 'codigo' => '811002', 'descricao' => 'Débitos de notas não processadas'],
            ['cst' => '811', 'codigo' => '811003', 'descricao' => 'Desenquadramento Simples Nacional'],

            // CST 820
            ['cst' => '820', 'codigo' => '820001', 'descricao' => 'Regime Específico - Planos de saúde'],
            ['cst' => '820', 'codigo' => '820002', 'descricao' => 'Regime Específico - Planos funerários'],
            ['cst' => '820', 'codigo' => '820003', 'descricao' => 'Regime Específico - Saúde animal'],
            ['cst' => '820', 'codigo' => '820004', 'descricao' => 'Regime Específico - Concursos de prognósticos'],
            ['cst' => '820', 'codigo' => '820005', 'descricao' => 'Regime Específico - Alienação de imóveis'],
            ['cst' => '820', 'codigo' => '820006', 'descricao' => 'Regime Específico - Exploração de via'],
            ['cst' => '820', 'codigo' => '820007', 'descricao' => 'Regime Específico - Serviços financeiros'],
            ['cst' => '820', 'codigo' => '820008', 'descricao' => 'Tributação em fatura anterior (Execução continuada)'],

            // CST 830
            ['cst' => '830', 'codigo' => '830001', 'descricao' => 'Exclusão base cálculo energia elétrica compensada (Microgeração)'],
        ];

        // Inserção em massa (Upsert)
        // Divide em chunks de 200 para garantir que rode em qualquer banco
        $chunks = array_chunk($classificacoes, 200);
        
        foreach ($chunks as $chunk) {
            foreach ($chunk as $item) {
                CClassTrib::updateOrCreate(
                    ['codigo' => $item['codigo']],
                    [
                        'descricao' => $item['descricao'],
                        'cst_compativel' => $item['cst']
                    ]
                );
            }
        }
    }
}