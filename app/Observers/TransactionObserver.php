<?php

namespace App\Observers;

use App\Models\TransactionLog;
use Illuminate\Support\Facades\Auth;

class TransactionObserver
{
    public function created($model)
    {
        $this->logTransacao('criar', null, $model);
    }

    public function updated($model)
    {
        $this->logTransacao('atualizar', $model->getOriginal(), $model);
    }

    public function deleted($model)
    {
        $this->logTransacao('deletar', $model, null);
    }

    private function logTransacao($acao, $dadosAnteriores, $dadosAtuais)
    {
        $usuarioId = Auth::id();
        $tabelaAfetada = class_basename($dadosAtuais);

        TransactionLog::create([
            'tabela_afetada' => $tabelaAfetada,
            'acao' => $acao,
            'dados_anteriores' => json_encode($dadosAnteriores),
            'dados_atuais' => json_encode($dadosAtuais),
            'usuario_id' => $usuarioId,
        ]);
    }
}
