<?php

namespace App\Console\Commands;

use App\Models\MDFE;
use App\Models\MDFeXml;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackfillMDFeXmls extends Command
{
    protected $signature = 'mdfe:backfill-xml {--dry-run : Apenas contabiliza os arquivos, sem gravar no banco}';

    protected $description = 'Migra os XMLs de MDF-e salvos em public/xml_mdfe para a tabela mdfe_xmls';

    private const PASTA_PARA_TIPO = [
        'Autorizadas' => MDFeXml::TIPO_AUTORIZADO,
        'Encerradas' => MDFeXml::TIPO_ENCERRADO,
        'Canceladas' => MDFeXml::TIPO_CANCELADO,
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $encontrados = 0;
        $gravados = 0;
        $semMDFe = 0;

        foreach (self::PASTA_PARA_TIPO as $pasta => $tipo) {
            $padrao = public_path("xml_mdfe/*/*/*/notas/{$pasta}/*.xml");
            $arquivos = glob($padrao) ?: [];
            $this->info(count($arquivos)." arquivo(s) encontrado(s) em notas/{$pasta}.");

            foreach ($arquivos as $arquivo) {
                $encontrados++;
                $chave = pathinfo($arquivo, PATHINFO_FILENAME);
                $mdfe = MDFE::where('chave_acesso', $chave)->first();

                if (! $mdfe) {
                    $semMDFe++;
                    $this->warn("  Pulado (sem MDF-e correspondente): {$chave} [{$tipo}]");

                    continue;
                }

                if (! $dryRun) {
                    MDFeXml::updateOrCreate(
                        ['mdfe_id' => $mdfe->id, 'tipo' => $tipo],
                        ['xml' => File::get($arquivo)]
                    );
                }

                $gravados++;
            }
        }

        $this->newLine();
        $this->info($dryRun ? 'Resumo da simulação:' : 'Resumo:');
        $this->info("  Arquivos encontrados: {$encontrados}");
        $this->info('  '.($dryRun ? 'Prontos para gravar' : 'Gravados no banco').": {$gravados}");
        $this->info("  Sem MDF-e correspondente: {$semMDFe}");

        return self::SUCCESS;
    }
}
