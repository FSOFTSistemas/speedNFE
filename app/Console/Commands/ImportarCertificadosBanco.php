<?php

namespace App\Console\Commands;

use App\Models\Empresa;
use App\Services\EmpresaCertificate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ImportarCertificadosBanco extends Command
{
    protected $signature = 'certificados:importar-banco
                            {--empresa= : Importa somente a empresa informada}
                            {--delete-files : Apaga o PFX legado somente após validar e gravar no banco}';

    protected $description = 'Importa certificados PFX legados para a coluna criptografada do banco';

    public function handle(EmpresaCertificate $certificates): int
    {
        if (! Schema::hasColumn('empresas', 'certificado_conteudo')) {
            $this->error('Execute primeiro a migration de certificado em banco.');

            return self::FAILURE;
        }

        $query = Empresa::query()->orderBy('id');
        if ($this->option('empresa')) {
            $query->whereKey((int) $this->option('empresa'));
        }

        $importados = 0;
        $ignorados = 0;
        $falhas = 0;

        $query->each(function (Empresa $empresa) use ($certificates, &$importados, &$ignorados, &$falhas) {
            if ($empresa->certificado_conteudo) {
                if ($this->option('delete-files')) {
                    try {
                        $legacyPath = $certificates->legacyPath($empresa);
                        $certificates->validate(
                            $certificates->content($empresa),
                            (string) $empresa->senhaCertificado
                        );
                        $empresa->certificado = null;
                        $empresa->save();

                        if ($legacyPath && is_file($legacyPath) && ! unlink($legacyPath)) {
                            throw new \RuntimeException('O certificado foi validado no banco, mas o arquivo legado não pôde ser removido.');
                        }

                        $ignorados++;
                        $this->info("Empresa {$empresa->id}: certificado do banco validado e arquivo legado removido.");
                    } catch (Throwable $e) {
                        $falhas++;
                        $this->error("Empresa {$empresa->id}: {$e->getMessage()}");
                    }

                    return;
                }

                $ignorados++;
                $this->line("Empresa {$empresa->id}: certificado já está no banco.");

                return;
            }

            try {
                $legacyPath = $certificates->legacyPath($empresa);
                $content = $certificates->content($empresa);
                $certificates->validate($content, (string) $empresa->senhaCertificado);

                $empresa->certificado_conteudo = $content;
                if ($this->option('delete-files')) {
                    $empresa->certificado = null;
                }
                $empresa->save();

                if ($this->option('delete-files') && $legacyPath && is_file($legacyPath) && ! unlink($legacyPath)) {
                    throw new \RuntimeException('O certificado foi importado, mas o arquivo legado não pôde ser removido.');
                }

                $importados++;
                $this->info("Empresa {$empresa->id}: certificado importado e validado.");
            } catch (Throwable $e) {
                $falhas++;
                $this->error("Empresa {$empresa->id}: {$e->getMessage()}");
            }
        });

        $this->newLine();
        $this->line("Importados: {$importados}; já existentes: {$ignorados}; falhas: {$falhas}.");

        return $falhas === 0 ? self::SUCCESS : self::FAILURE;
    }
}
