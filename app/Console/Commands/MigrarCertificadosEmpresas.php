<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MigrarCertificadosEmpresas extends Command
{
    protected $signature = 'certificado:migrar';

    protected $description = 'Move os certificados .pfx pra fora da pasta pública e criptografa as senhas de certificado já salvas em texto puro';

    public function handle()
    {
        $this->migrarArquivos();
        $this->migrarSenhas();

        return 0;
    }

    private function migrarArquivos()
    {
        $origem = storage_path('app/public/certificados');
        $destino = storage_path('app/certificados');

        if (!File::isDirectory($origem)) {
            $this->info('Nenhuma pasta antiga de certificados encontrada (nada pra mover).');
            return;
        }

        if (!File::isDirectory($destino)) {
            File::makeDirectory($destino, 0755, true);
        }

        $arquivos = File::files($origem);
        $movidos = 0;

        foreach ($arquivos as $arquivo) {
            $novoCaminho = $destino . '/' . $arquivo->getFilename();

            if (File::exists($novoCaminho)) {
                $this->warn("  Já existe no destino, pulado: {$arquivo->getFilename()}");
                continue;
            }

            File::move($arquivo->getPathname(), $novoCaminho);
            $movidos++;
        }

        $this->info("Arquivos movidos para storage/app/certificados: {$movidos}");
    }

    private function migrarSenhas()
    {
        $empresas = DB::table('empresas')->whereNotNull('senhaCertificado')->where('senhaCertificado', '!=', '')->get(['id', 'senhaCertificado']);

        $jaEncriptadas = 0;
        $encriptadasAgora = 0;

        foreach ($empresas as $empresa) {
            try {
                Crypt::decryptString($empresa->senhaCertificado);
                $jaEncriptadas++;
                continue;
            } catch (DecryptException $e) {
                // Não era um valor criptografado - está em texto puro, precisa encriptar.
            }

            DB::table('empresas')
                ->where('id', $empresa->id)
                ->update(['senhaCertificado' => Crypt::encryptString($empresa->senhaCertificado)]);

            $encriptadasAgora++;
        }

        $this->info("Senhas já criptografadas: {$jaEncriptadas}");
        $this->info("Senhas criptografadas agora: {$encriptadasAgora}");
    }
}
