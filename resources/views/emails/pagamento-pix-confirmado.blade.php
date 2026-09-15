@component('mail::message')
# Olá, {{ $nomeUsuario }}!

Seu pagamento foi confirmado com sucesso.

@component('mail::panel')
Recebemos a confirmação do seu pagamento referente a **{{ $descricao }}** no valor de **R$ {{ $valor }}**.
@endcomponent

**Identificação do Pagamento (TXID):**
`{{ $txid }}`

Obrigado por sua confiança!

Atenciosamente,<br>
F-Soft Sistemas.
@endcomponent