<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PagamentoPixConfirmado extends Mailable
{
    use Queueable, SerializesModels;

    // Propriedades públicas para os dados do e-mail
    public string $nomeUsuario;
    public string $txid;
    public string $valor;
    public string $descricao;

    /**
     * Crie uma nova instância da mensagem.
     */
    public function __construct(string $nomeUsuario, string $txid, string $valor, string $descricao)
    {
        $this->nomeUsuario = $nomeUsuario;
        $this->txid = $txid;
        $this->valor = $valor;
        $this->descricao = $descricao;
    }

    /**
     * Construa a mensagem.
     */
    public function build()
    {
        return $this->subject('Pagamento Confirmado!')
                    ->markdown('emails.pagamento-pix-confirmado');
    }
}