<?php

namespace App\Console\Commands;

use App\Models\Pedido;
use App\Models\PedidoXml;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackfillPedidoXmls extends Command
{
    protected $signature = 'nfe:backfill-xml';

    protected $description = 'Migra os XMLs de NFe salvos em public/{empresa}/{ano}/{mes}/notas/{tipo}/{chave}.xml para a tabela pedido_xmls';

    private const PASTA_PARA_TIPO = [
        'Autorizadas' => 'autorizado',
        'Canceladas' => 'cancelado',
        'CCe' => 'cce',
    ];

    public function handle()
    {
        $encontrados = 0;
        $vinculados = 0;
        $gravados = 0;
        $semPedido = 0;

        foreach (self::PASTA_PARA_TIPO as $pasta => $tipo) {
            $arquivos = glob(public_path("*/*/*/notas/{$pasta}/*.xml"));

            if (!$arquivos) {
                $this->info("Nenhum arquivo encontrado em notas/{$pasta}.");
                continue;
            }

            $this->info(count($arquivos) . " arquivo(s) encontrado(s) em notas/{$pasta}.");

            foreach ($arquivos as $arquivo) {
                $encontrados++;
                $chave = pathinfo($arquivo, PATHINFO_FILENAME);

                $pedido = Pedido::where('chave', $chave)->first();

                if (!$pedido) {
                    $semPedido++;
                    $this->warn("  Pulado (sem pedido correspondente): {$chave} [{$tipo}]");
                    continue;
                }

                $vinculados++;

                PedidoXml::updateOrCreate(
                    ['pedido_id' => $pedido->id, 'tipo' => $tipo],
                    ['xml' => File::get($arquivo)]
                );

                $gravados++;
            }
        }

        $this->newLine();
        $this->info('Resumo:');
        $this->info("  Arquivos encontrados: {$encontrados}");
        $this->info("  Vinculados a um pedido: {$vinculados}");
        $this->info("  Gravados no banco: {$gravados}");
        $this->info("  Sem pedido correspondente: {$semPedido}");

        return 0;
    }
}
