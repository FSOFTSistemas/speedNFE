<?php

namespace App\Console\Commands;

use App\Models\Empresa;
use App\Models\Servico;
use App\Services\EmpresaCertificate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class DiagnosticoNFSe extends Command
{
    protected $signature = 'nfse:diagnostico
                            {--empresa= : ID da empresa emissora}
                            {--servico= : ID do serviço que será usado no teste}';

    protected $description = 'Valida a configuração local necessária para emitir NFS-e Nacional';

    private int $failures = 0;

    public function handle(EmpresaCertificate $certificates): int
    {
        $empresaId = (int) $this->option('empresa');
        if ($empresaId <= 0) {
            $this->error('Informe a empresa com --empresa=ID.');

            return self::FAILURE;
        }

        if (! $this->checkDatabase()) {
            return self::FAILURE;
        }

        $empresa = Empresa::query()->with('endereco')->find($empresaId);
        if (! $empresa) {
            $this->error("Empresa {$empresaId} não encontrada.");

            return self::FAILURE;
        }

        $this->checkCompany($empresa, $certificates);
        $this->checkResources();
        $this->checkDomains();
        $this->checkService($empresa);
        $this->checkUrls();

        $this->newLine();
        if ($this->failures > 0) {
            $this->error("Diagnóstico concluído com {$this->failures} pendência(s).");

            return self::FAILURE;
        }

        $this->info('Diagnóstico concluído sem pendências locais. Nenhum documento fiscal foi transmitido.');

        return self::SUCCESS;
    }

    private function checkDatabase(): bool
    {
        $requiredTables = ['empresas', 'nfses', 'nfse_xmls', 'nfse_eventos', 'nfse_servicos_nacionais', 'nfse_nbs', 'nfse_ind_ops'];
        foreach ($requiredTables as $table) {
            if (! Schema::hasTable($table)) {
                $this->fail("Tabela ausente: {$table}.");
            }
        }

        foreach (['certificado_conteudo', 'ultimaNFSe', 'ultimaDPS', 'serieNFSe', 'inscricao_municipal'] as $column) {
            if (Schema::hasTable('empresas') && ! Schema::hasColumn('empresas', $column)) {
                $this->fail("Coluna ausente em empresas: {$column}.");
            }
        }

        foreach (['cst_ibs_cbs', 'finNFSe', 'indFinal', 'indDest'] as $column) {
            if (Schema::hasTable('nfses') && ! Schema::hasColumn('nfses', $column)) {
                $this->fail("Coluna ausente em nfses: {$column}.");
            }
        }

        if ($this->failures > 0) {
            return false;
        }

        $this->pass('Estrutura do banco de dados encontrada.');

        return true;
    }

    private function checkCompany(Empresa $empresa, EmpresaCertificate $certificates): void
    {
        $document = preg_replace('/\D/', '', (string) $empresa->cpf_cnpj);
        $this->require(in_array(strlen($document), [11, 14], true), 'CPF/CNPJ da empresa configurado.');
        $this->require((string) $empresa->inscricao_municipal !== '', 'Inscrição municipal configurada.');
        $this->require((bool) ($empresa->endereco?->codigoIBGE), 'Código IBGE da empresa configurado.');
        $this->require((bool) ($empresa->serieNFSe ?: $empresa->serie), 'Série da DPS/NFS-e configurada.');
        $this->require(is_numeric($empresa->ultimaDPS), 'Último número da DPS configurado.');
        $this->require(in_array((int) $empresa->ambiente, [1, 2], true), 'Ambiente fiscal válido.');

        try {
            $content = $certificates->content($empresa);
            $certificates->validate($content, (string) $empresa->senhaCertificado);
            $this->pass('Certificado do banco e senha validados.');
        } catch (Throwable $e) {
            $this->fail('Certificado inválido: '.$e->getMessage());
        }

        $environment = (int) $empresa->ambiente === 1 ? 'PRODUÇÃO' : 'PRODUÇÃO RESTRITA';
        $this->warn("Ambiente da empresa: {$environment}.");
    }

    private function checkResources(): void
    {
        $before = $this->failures;
        $schemas = [
            'DPS_v1.01.xsd',
            'NFSe_v1.01.xsd',
            'pedRegEvento_v1.01.xsd',
            'evento_v1.01.xsd',
            'tiposComplexos_v1.01.xsd',
            'tiposEventos_v1.01.xsd',
            'tiposSimples_v1.01.xsd',
            'xmldsig-core-schema.xsd',
        ];

        foreach ($schemas as $schema) {
            if (! is_file(resource_path('schemas/nfse/v1.01/'.$schema))) {
                $this->fail("Schema ausente: {$schema}.");
            }
        }

        if ($this->failures === $before) {
            $this->pass('Schemas oficiais da NFS-e encontrados.');
        }
    }

    private function checkDomains(): void
    {
        $before = $this->failures;
        foreach (['nfse_servicos_nacionais', 'nfse_nbs', 'nfse_ind_ops'] as $table) {
            if (Schema::hasTable($table) && DB::table($table)->count() === 0) {
                $this->fail("Domínio sem dados: {$table}. Execute nfse:importar-dominios.");
            }
        }

        if ($this->failures === $before) {
            $this->pass('Domínios nacionais importados.');
        }
    }

    private function checkService(Empresa $empresa): void
    {
        if (! $this->option('servico')) {
            $this->warn('Serviço não verificado. Para validar um serviço, informe --servico=ID.');

            return;
        }

        $service = Servico::query()
            ->whereKey((int) $this->option('servico'))
            ->where('empresa_id', $empresa->id)
            ->first();
        if (! $service) {
            $this->fail('Serviço não encontrado para a empresa informada.');

            return;
        }

        foreach (['cTribNac', 'cIndOp', 'cClassTrib', 'cst_ibs_cbs', 'tribISSQN', 'tpRetISSQN'] as $field) {
            $this->require((string) $service->{$field} !== '', "Serviço com {$field} configurado.");
        }
    }

    private function checkUrls(): void
    {
        $urls = [
            'SEFIN restrita' => config('nfse.sefin.restrita_base_url'),
            'SEFIN produção' => config('nfse.sefin.producao_base_url'),
            'DANFSe' => config('nfse.danfse.base_url'),
        ];

        foreach ($urls as $label => $url) {
            $this->require(is_string($url) && str_starts_with($url, 'https://'), "URL {$label} usa HTTPS.");
        }
    }

    private function require(bool $condition, string $message): void
    {
        $condition ? $this->pass($message) : $this->fail($message);
    }

    private function pass(string $message): void
    {
        $this->line("[OK] {$message}");
    }

    private function fail(string $message): void
    {
        $this->failures++;
        $this->error("[FALHA] {$message}");
    }
}
