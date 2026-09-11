# Integração Efí (Gerencianet) — PIX de cobrança / mensalidade

Guia passo a passo para replicar em outro projeto Laravel a integração PIX que já roda
neste sistema (SpeedNFE). Cobre: SDK, credenciais, certificado, service, controller,
tela com QR Code + polling, tela de sucesso e webhook.

O padrão implementado aqui é:

1. Front dispara `POST /pagamento` com `valor`, `descricao` e (opcional) dados da fatura.
2. Backend abre a tela do QR Code.
3. JS da tela chama `POST /pix/cob` → backend cria a **cobrança imediata** na Efí e devolve QR Code + copia-e-cola.
4. JS faz **polling** em `GET /pix/cob/{txid}` a cada 3s até o status virar `CONCLUIDA`.
5. Ao confirmar, o JS dá baixa da fatura (aqui: numa API financeira externa) e redireciona para a tela de sucesso.
6. (Opcional / recomendado) Webhook da Efí em `POST /api/pix/webhook` para conciliação server-side.

---

## 1. Dependências

```bash
composer require efipay/sdk-php-apis-efi:^1.17
```

O namespace do SDK é `Efi\EfiPay` (`Efi\Exception\EfiException` para erros).

---

## 2. Credenciais e certificado

No painel da Efí (https://sejaefi.com.br) → API → Aplicações:

- Crie uma aplicação e habilite o escopo **PIX** (cobrança, QR Code, webhook).
- Gere um par **Client ID / Client Secret** (há um par para *Produção* e outro para *Homologação*).
- Baixe o **certificado `.p12`** (Meus Certificados). Ele é usado para mTLS na API PIX.
- Cadastre a **chave PIX** que vai receber (aleatória, e-mail, CPF/CNPJ ou telefone).

### Onde guardar o certificado

Neste projeto o caminho é **relativo ao `base_path()`** e resolvido com `realpath()`:

```php
'certificate' => realpath(base_path(config('efipay.certificate'))),
```

Coloque o arquivo fora do controle de versão, ex.: `storage/app/certificados/efi/producao.p12`
e aponte `EFI_CERT_PATH=storage/app/certificados/efi/producao.p12`.
Garanta que o processo PHP tenha permissão de leitura.

> Alguns certificados `.p12` da Efí vêm **sem senha** — nesse caso deixe `EFI_CERT_PASS` vazio/null.

---

## 3. Configuração

### `.env`

```dotenv
EFI_CLIENT_ID=Client_Id_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
EFI_CLIENT_SECRET=Client_Secret_xxxxxxxxxxxxxxxxxxxxxxxxxxxx
EFI_CERT_PATH=storage/app/certificados/efi/producao.p12
EFI_CERT_PASS=
EFI_PIX_SANDBOX=true          # true = homologação, false = produção
EFI_PIX_KEY=sua-chave-pix
EFI_WEBHOOK_BASE=https://seu-dominio.com.br
```

### `config/efipay.php`

```php
<?php
return [
    'client_id'        => env('EFI_CLIENT_ID'),
    'client_secret'    => env('EFI_CLIENT_SECRET'),
    'certificate'      => env('EFI_CERT_PATH'),        // relativo ao base_path()
    'certificate_pass' => env('EFI_CERT_PASS', null),
    'sandbox'          => filter_var(env('EFI_PIX_SANDBOX', true), FILTER_VALIDATE_BOOL),
    'pix_key'          => env('EFI_PIX_KEY'),
    'webhook_base'     => rtrim(env('EFI_WEBHOOK_BASE', ''), '/'),
];
```

Depois de mexer no `.env` em produção: `php artisan config:cache`.

---

## 4. Service — `app/Services/EfiPixService.php`

Encapsula o SDK. Toda chamada à Efí passa por aqui.

```php
<?php

namespace App\Services;

use Efi\EfiPay;

class EfiPixService
{
    private EfiPay $client;

    public function __construct()
    {
        $options = [
            'clientId'       => config('efipay.client_id'),
            'clientSecret'   => config('efipay.client_secret'),
            // caminho ABSOLUTO para o certificado:
            'certificate'    => realpath(base_path(config('efipay.certificate'))),
            'pwdCertificate' => config('efipay.certificate_pass'),
            'sandbox'        => config('efipay.sandbox'),
        ];

        $this->client = new EfiPay($options);
    }

    /** POST /v2/cob — cobrança imediata */
    public function criarCobranca(array $payload): array
    {
        return $this->client->pixCreateImmediateCharge([], $payload);
    }

    /** GET /v2/loc/{id}/qrcode — imagem base64 + copia-e-cola */
    public function obterQrCodePorLocId(int $locId): array
    {
        return $this->client->pixGenerateQRCode(['id' => $locId], []);
    }

    /** GET /v2/cob/{txid} — status da cobrança */
    public function consultarCobranca(string $txid): array
    {
        return $this->client->pixDetailCharge(['txid' => $txid]);
    }

    /** PUT /v2/webhook/{chave} — registra a URL do webhook */
    public function configurarWebhook(string $pixKey, string $url, bool $skipMtls = false): array
    {
        $headers = $skipMtls ? ['x-skip-mtls-checking' => 'true'] : [];
        return $this->client->pixConfigWebhook(
            ['chave' => $pixKey],
            ['webhookUrl' => $url],
            $headers
        );
    }
}
```

Métodos do SDK usados (nomes exatos):

| Método SDK | Endpoint Efí | Uso |
|---|---|---|
| `pixCreateImmediateCharge($params, $body)` | `POST /v2/cob` | cria cobrança |
| `pixGenerateQRCode(['id' => $locId])` | `GET /v2/loc/{id}/qrcode` | gera QR Code |
| `pixDetailCharge(['txid' => $txid])` | `GET /v2/cob/{txid}` | consulta status |
| `pixConfigWebhook(['chave' => $key], ['webhookUrl' => $url], $headers)` | `PUT /v2/webhook/{chave}` | registra webhook |

---

## 5. Controller — `app/Http/Controllers/PixController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EfiPixService;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Throwable;

class PixController extends Controller
{
    public function __construct(private EfiPixService $efi) {}

    /** GET/POST /pagamento — abre a tela do QR Code */
    public function pagar(Request $request)
    {
        return view('faturas.payment', [
            'valor'             => $request->input('valor'),
            'descricao'         => $request->input('descricao', 'Pagamento de Mensalidade'),
            'expiracao'         => $request->input('expiracao', 300),   // segundos
            'installment_id'    => $request->input('installment_id'),   // opcional: id da fatura
            'customer_cnpj_cpf' => $request->input('customer_cnpj_cpf'),
        ]);
    }

    /** POST /pix/cob — cria a cobrança e devolve QR Code */
    public function criar(Request $req)
    {
        try {
            $data = $req->validate([
                'valor'     => 'required|numeric|min:0.01',
                'descricao' => 'nullable|string|max:255',
                'expiracao' => 'nullable|integer|min:60|max:86400',
            ]);

            $valor     = number_format((float) $data['valor'], 2, '.', '');
            $expiracao = (int) ($data['expiracao'] ?? 300);

            $payload = [
                'calendario'         => ['expiracao' => $expiracao],
                'valor'              => ['original' => $valor],
                'chave'              => config('efipay.pix_key'),
                'solicitacaoPagador' => $data['descricao'] ?? 'Pagamento',
            ];

            $cobranca = $this->efi->criarCobranca($payload);
            $locId    = data_get($cobranca, 'loc.id');

            if (!$locId) {
                return response()->json(['message' => 'Falha ao gerar cobrança PIX: location não retornado.'], 500);
            }

            $qrcode    = $this->efi->obterQrCodePorLocId($locId);
            $criacao   = Carbon::parse(data_get($cobranca, 'calendario.criacao'));
            $expiresAt = $criacao->copy()->addSeconds($expiracao)->toIso8601String();

            return response()->json([
                'txid'         => data_get($cobranca, 'txid'),
                'status'       => data_get($cobranca, 'status'),
                'location'     => data_get($cobranca, 'loc.location'),
                'qr_base64'    => data_get($qrcode, 'imagemQrcode'),   // data:image/png;base64,...
                'copia_e_cola' => data_get($qrcode, 'qrcode'),
                'expires_at'   => $expiresAt,
            ]);
        } catch (ValidationException $ve) {
            return response()->json(['message' => $ve->getMessage(), 'errors' => $ve->errors()], 422);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erro ao criar cobrança PIX', 'error' => $e->getMessage()], 500);
        }
    }

    /** GET /pix/cob/{txid} — consulta status (usado pelo polling) */
    public function consultar(string $txid)
    {
        try {
            $cobranca  = $this->efi->consultarCobranca($txid);
            $status    = strtoupper((string) data_get($cobranca, 'status', ''));
            $pago      = in_array($status, ['CONCLUIDA', 'LIQUIDADA', 'COMPLETA']);
            $criacao   = data_get($cobranca, 'calendario.criacao');
            $exp       = (int) data_get($cobranca, 'calendario.expiracao', 3600);
            $expiresAt = $criacao ? Carbon::parse($criacao)->addSeconds($exp)->toIso8601String() : null;

            return response()->json([
                'status'     => $status,
                'pago'       => $pago,
                'txid'       => data_get($cobranca, 'txid'),
                'valor'      => data_get($cobranca, 'valor.original'),
                'expires_at' => $expiresAt,
                'raw'        => $cobranca,
            ]);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erro ao consultar cobrança PIX', 'error' => $e->getMessage()], 500);
        }
    }
}
```

### Formato da resposta da Efí ao criar cobrança (campos relevantes)

```json
{
  "txid": "abc123...",
  "status": "ATIVA",
  "calendario": { "criacao": "2026-01-01T12:00:00.000Z", "expiracao": 300 },
  "valor": { "original": "49.90" },
  "loc": { "id": 12345, "location": "pix.example.com/qr/v2/..." }
}
```

E o `pixGenerateQRCode` devolve `imagemQrcode` (PNG em base64, já com prefixo `data:`) e `qrcode` (o "copia e cola").

Status possíveis de `GET /v2/cob/{txid}`: `ATIVA`, `CONCLUIDA` (pago), `REMOVIDA_PELO_USUARIO_RECEBEDOR`, `REMOVIDA_PELO_PSP`.

---

## 6. Rotas

### `routes/web.php`

```php
use App\Http\Controllers\PixController;

// Tela do QR Code
Route::match(['get', 'post'], '/pagamento', [PixController::class, 'pagar'])->name('pix.pagamento');

// API interna consumida pelo JS da tela
Route::post('/pix/cob',        [PixController::class, 'criar'])->name('pix.criar');
Route::get('/pix/cob/{txid}',  [PixController::class, 'consultar'])->name('pix.consultar');

// Tela de sucesso
Route::get('/pagamento/sucesso', function (\Illuminate\Http\Request $req) {
    return view('faturas.pagamento-sucesso', [
        'txid'      => $req->query('txid'),
        'valor'     => $req->query('valor'),
        'descricao' => $req->query('descricao'),
    ]);
})->name('pix.sucesso');
```

> Se as rotas `/pix/cob` estiverem no grupo `web`, o JS precisa mandar o header
> `X-CSRF-TOKEN` (o exemplo abaixo já faz isso). O `GET` de consulta não precisa.

### `routes/api.php` (webhook)

```php
use App\Http\Controllers\PixWebhookController;

Route::post('/pix/webhook', [PixWebhookController::class, 'receber']);
```

O webhook **não** pode ter `auth` — a Efí chama sem token. Ele se autovalida (ver seção 9).
Se estiver em `routes/web.php`, adicione a URI em `App\Http\Middleware\VerifyCsrfToken::$except`.
Em `routes/api.php` já fica fora do CSRF.

---

## 7. View da tela de pagamento — `resources/views/faturas/payment.blade.php`

Pontos-chave da tela (ver arquivo completo no projeto SpeedNFE):

1. Recebe do controller: `valor`, `descricao`, `expiracao`, `installment_id`, `customer_cnpj_cpf`.
2. Ao carregar (`DOMContentLoaded`), chama `criarPix()`.
3. `criarPix()` → `fetch(POST /pix/cob)` com `{valor, descricao, expiracao}` e header `X-CSRF-TOKEN`.
4. Renderiza `qr_base64` num `<img>` e `copia_e_cola` num `<textarea>`.
5. `iniciarPolling()` → `setInterval` de 3s chamando `GET /pix/cob/{txid}`.
6. Quando `data.pago === true`: para os timers, dá baixa (`marcarFaturaPagaAPI`), redireciona para `pix.sucesso` com querystring.
7. Countdown/progress bar baseados em `expires_at`.

Esqueleto mínimo do JS (sem o visual):

```html
<script>
(function () {
    const S = {
        valor:     @json($valor),
        descricao: @json($descricao),
        expiracao: @json((int) $expiracao),
        installment_id:    @json($installment_id),
        customer_cnpj_cpf: @json($customer_cnpj_cpf),
        rotas: {
            criar:         @json(route('pix.criar')),
            consultarBase: @json(url('/pix/cob')),
            sucesso:       @json(route('pix.sucesso')),
        },
        csrf: @json(csrf_token()),
    };

    let txid = null, pago = false, pollTimer = null;

    async function criarPix() {
        const resp = await fetch(S.rotas.criar, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': S.csrf },
            body: JSON.stringify({ valor: S.valor, descricao: S.descricao, expiracao: S.expiracao }),
        });
        if (!resp.ok) throw new Error(await resp.text());
        const data = await resp.json();

        txid = data.txid;
        document.querySelector('#qrImg').src = data.qr_base64;
        document.querySelector('#copiaCola').value = data.copia_e_cola;

        iniciarPolling();
    }

    function iniciarPolling() {
        pollTimer = setInterval(async () => {
            if (!txid) return;
            const resp = await fetch(`${S.rotas.consultarBase}/${encodeURIComponent(txid)}`, {
                headers: { 'Accept': 'application/json' },
            });
            if (!resp.ok) return;
            const data = await resp.json();

            if (data.pago && !pago) {
                pago = true;
                clearInterval(pollTimer);

                const dados = {
                    txid: txid,
                    valor: Number(S.valor).toFixed(2),
                    descricao: S.descricao || 'Pagamento',
                };
                await marcarFaturaPagaAPI(dados);                       // baixa da fatura
                const qs = new URLSearchParams(dados).toString();
                window.location.href = `${S.rotas.sucesso}?${qs}`;
            }
        }, 3000);
    }

    // Baixa da fatura — AJUSTE para o seu backend.
    // No SpeedNFE isso chama uma API financeira externa direto do browser.
    // O ideal é ter um endpoint próprio (ex.: POST /faturas/{id}/baixar) OU
    // usar exclusivamente o webhook (seção 9) e remover esta função.
    async function marcarFaturaPagaAPI(dados) {
        if (!S.installment_id) return;
        try {
            await fetch('/faturas/baixar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': S.csrf },
                body: JSON.stringify({
                    installment_id: S.installment_id,
                    txid: dados.txid,
                    amount: dados.valor,
                    paid_at: new Date().toISOString().split('T')[0],
                    payment_method: 'pix',
                }),
            });
        } catch (e) {
            console.error('Falha ao dar baixa na fatura', e);
        }
    }

    document.addEventListener('DOMContentLoaded', criarPix);
})();
</script>
```

### Como o usuário chega nessa tela

Um form simples (na tela de faturas/assinatura):

```html
<form method="POST" action="{{ route('pix.pagamento') }}">
    @csrf
    <input type="hidden" name="valor" value="{{ $fatura->valor }}">
    <input type="hidden" name="descricao" value="Mensalidade {{ $fatura->competencia }}">
    <input type="hidden" name="installment_id" value="{{ $fatura->id }}">
    <input type="hidden" name="customer_cnpj_cpf" value="{{ $empresa->cpf_cnpj }}">
    <button type="submit" class="btn btn-primary">Pagar com PIX</button>
</form>
```

---

## 8. Tela de sucesso — `resources/views/faturas/pagamento-sucesso.blade.php`

Recebe `txid`, `valor`, `descricao` pela querystring e mostra o comprovante.
Neste projeto há ainda um envio opcional de comprovante por e-mail
(`PixController::enviarConfirmacaoEmail` + Mailable `PagamentoPixConfirmado`,
rota `POST /pagamento/enviar-confirmacao`). É acessório — pode ser omitido.

---

## 9. Webhook (recomendado para produção) — `app/Http/Controllers/PixWebhookController.php`

O polling no browser só funciona enquanto a aba está aberta. Para conciliação
confiável, registre o webhook da Efí.

### Registrar a URL (uma vez, via tinker ou command)

```php
app(\App\Services\EfiPixService::class)->configurarWebhook(
    config('efipay.pix_key'),
    config('efipay.webhook_base') . '/api/pix/webhook',
    skipMtls: true   // true se você NÃO for validar o certificado mTLS da Efí
);
```

> A Efí acrescenta `/pix` ao final da URL registrada por padrão. Com
> `x-skip-mtls-checking: true` ela chama exatamente a URL informada. Escolha uma
> abordagem e mantenha a rota coerente.

### Controller

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\EfiPixService;

class PixWebhookController extends Controller
{
    public function receber(Request $request, EfiPixService $efi)
    {
        $payload = $request->all();
        Log::info('PIX webhook recebido', $payload);

        // A Efí envia: { "pix": [ { "txid": "...", "endToEndId": "...", "valor": "...", "horario": "..." }, ... ] }
        foreach (data_get($payload, 'pix', []) as $pix) {
            $txid = $pix['txid'] ?? null;
            if (!$txid) {
                continue;
            }

            // Reconsulta na Efí para confirmar (não confie só no corpo recebido)
            $cobranca = $efi->consultarCobranca($txid);
            $status   = strtoupper((string) data_get($cobranca, 'status', ''));

            if (in_array($status, ['CONCLUIDA', 'LIQUIDADA', 'COMPLETA'])) {
                // TODO: localizar a fatura pelo txid e marcar como paga (idempotente!)
                // Fatura::where('txid', $txid)->where('status', '!=', 'pago')->update([...]);
            }
        }

        return response()->json(['ok' => true]);
    }
}
```

Boas práticas do webhook:

- **Idempotência**: a Efí pode reenviar. Só marque como pago se ainda não estiver.
- **Reconsulte** a cobrança na API (`consultarCobranca`) em vez de confiar no corpo.
- Responda **HTTP 200** rápido; processe pesado em fila se precisar.
- Sem `auth`/CSRF na rota.
- Persista o `txid` na sua fatura no momento do `criar()` para conseguir casar depois.

---

## 10. Checklist de implantação num projeto novo

- [ ] `composer require efipay/sdk-php-apis-efi`
- [ ] Criar aplicação na Efí, habilitar escopo PIX, gerar Client ID/Secret (prod e homolog)
- [ ] Baixar certificado `.p12` e colocar em `storage/app/certificados/efi/` (fora do git)
- [ ] Cadastrar chave PIX recebedora
- [ ] `config/efipay.php` + variáveis no `.env` (+ `.env.example` sem valores)
- [ ] `EfiPixService`
- [ ] `PixController` (`pagar`, `criar`, `consultar`)
- [ ] Rotas web (`/pagamento`, `/pix/cob`, `/pix/cob/{txid}`, `/pagamento/sucesso`)
- [ ] Views `payment.blade.php` e `pagamento-sucesso.blade.php`
- [ ] Coluna `txid` (e status/paid_at) na tabela de faturas/parcelas
- [ ] Endpoint próprio de baixa OU webhook (`/api/pix/webhook`) — de preferência o webhook
- [ ] Registrar webhook na Efí (`configurarWebhook`)
- [ ] Testar em **sandbox** (`EFI_PIX_SANDBOX=true`) antes de produção
- [ ] Em produção: `EFI_PIX_SANDBOX=false`, `php artisan config:cache`

---

## 11. Diferenças / pontos a melhorar em relação ao SpeedNFE

Neste projeto (origem), a baixa da fatura é feita **no front**, via `fetch` direto
do browser para uma API financeira externa (`financeiro.f-softsistemas.com.br/api/customer/installments/pay`),
com falhas silenciosas, e o webhook (`PixWebhookController`) só loga (`// TODO: conciliar pagamento`).

Ao portar, prefira:

1. Guardar `txid` na fatura no `PixController::criar()`.
2. Fazer a baixa **no backend**, acionada pelo **webhook** (fonte de verdade).
3. Deixar o polling do front apenas para atualizar a UI / redirecionar.
4. Tratar erros de baixa (log + alerta + retry/fila), não silenciar.
