<?php

namespace App\Observers;

use App\Models\TransactionLog;
use Illuminate\Support\Facades\Auth;

class TransactionObserver
{
    public function created($model)
    {
        $this->logTransacao('criar', null, $model, class_basename($model));
    }

    public function updated($model)
    {
        $this->logTransacao('atualizar', $model->getOriginal(), $model->getOriginal(), class_basename($model));
    }

    public function deleted($model)
    {
        $this->logTransacao('deletar', $model, $model, class_basename($model));
    }

    private function logTransacao($acao, $dadosAnteriores, $dadosAtuais, $tabelaAfetada)
    {
        $usuarioId = Auth::id();
        TransactionLog::create([
            'tabela_afetada' => $tabelaAfetada,
            'acao' => $acao,
            'dados_anteriores' => json_encode($dadosAnteriores),
            'dados_atuais' => $acao == 'deletar' ? null : json_encode($dadosAtuais),
            'usuario_id' => $usuarioId,
        ]);
    }
}
