# Cadastro autônomo de clientes (self-signup) — análise e plano

> Status: **planejado** · Análise feita em 24/09/2026 · **Fase 0 concluída em 24/09/2026** (rotas `/register` removidas); fases 1–4 não iniciadas.

Objetivo: permitir que um novo cliente crie a própria conta (empresa + usuário admin) sem depender do
cadastro manual feito pelo `master`, mantendo cobrança, limites de plano e isolamento entre empresas.

**Veredito: viável, esforço médio.** O maior bloqueio não é o formulário de cadastro, e sim a
**cobrança/assinatura**, que hoje depende de um sistema externo (financeiro FSOFT).

---

## ⚠️ Pendência de segurança (fazer antes de tudo)

As rotas `GET/POST /register` (`routes/auth.php`) estão **públicas e ativas**, e
`App\Http\Controllers\Auth\RegisteredUserController::store()` grava o usuário com
`'cargo' => $request->cargo` — ou seja, **o visitante escolhe o próprio cargo**, inclusive `master`.

Impacto hoje (limitado por acaso, não por design):

- O usuário é criado com `empresa_id = 0`.
- Na web, quase tudo fica atrás do `check.subscription` (`App\Http\Middleware\SignatureStatus`), que quebra
  ao acessar `Auth::user()->empresa->cpf_cnpj` com empresa inexistente.
- **A API `/api/v1` não passa pelo `check.subscription`**: esse usuário consegue logar via JWT e acessar os
  módulos que o cargo permite (`App\Support\Modulos`), com dados escopados à empresa 0. Os endpoints da API
  não foram auditados um a um.

**Ação (feita — Fase 0):** as rotas `GET/POST register` foram removidas de `routes/auth.php`,
`RegisteredUserController` deixou de gravar `cargo` da requisição e `tests/Feature/RegistroPublicoDesativadoTest.php`
garante que a rota não volte. A nova rota de cadastro nunca deve aceitar `cargo`, `tipo` ou `empresa_id`
vindos da requisição.

**Pendente:** verificar se alguém já se cadastrou pela rota aberta e inativar essas contas (ver consulta
abaixo). Obs.: `CheckUserStatus` (status `inativo`) só roda no grupo `web`; um JWT já emitido para a API
continua válido até expirar.

```sql
SELECT id, name, email, cargo, tipo, empresa_id, created_at
FROM users
WHERE empresa_id = 0 OR empresa_id NOT IN (SELECT id FROM empresas)
ORDER BY created_at DESC;
```

---

## Como uma conta nasce hoje

- Só o `master` cria empresas: `EmpresasController::store` (rota `empresa/cadastrar`,
  `access.permission:master`).
- O formulário exige tudo de uma vez: dados cadastrais, endereço com IBGE, **certificado digital + senha,
  CSC/IdCSC, série, ambiente, CRT**, **limites do plano digitados à mão** (`limClientes`, `limProdutos`,
  `limNFes`, `limNFCes`, `limNFSe`, `limMDFes`), o `cargo` (define os módulos liberados) e o usuário admin
  (`tipo = admin`).
- Na landing page (`resources/views/homePage.blade.php`), "Teste grátis" e "Assinar" levam ao WhatsApp — o
  processo comercial é 100% manual.

## O que já existe e ajuda

| Recurso | Onde | Uso no self-signup |
|---|---|---|
| Admin edita a própria empresa (inclusive certificado) | `empresa.editar` / `update_empresa` com `can:menu-administracao` | Etapa de configuração fiscal pode vir depois do cadastro |
| Consulta de CNPJ | `ClientesController::BuscarCnpj`, `Api\V1\ClientesController::consultarCnpj` | Autopreencher razão social e endereço |
| Busca por CEP (ViaCEP) e IBGE | formulários de empresa/NFS-e | Autopreencher endereço |
| Limites aplicados na criação | `contagemClientes`, `contagemProdutos`, `PedidosService::limiteDeNotas` | Limites do plano passam a valer automaticamente |
| Certificado criptografado no banco | `App\Services\EmpresaCertificate`, coluna `empresas.certificado_conteudo` | Upload seguro feito pelo próprio cliente |
| Pix (Efí) | `EfiPixService`, `PixController`, `docs/integracao-efi-pix.md` | Possível cobrança automática (Fase 4) |

## Lacunas

### 1. Cobrança / assinatura (maior risco)

`SignatureStatus` consulta `https://financeiro.f-softsistemas.com.br/api/customer/{cnpj}` e só bloqueia se
vier `due.expires === true` com vencimento + 3 dias de carência. **Se o CNPJ não existir no financeiro, não
há `due` e o acesso é liberado indefinidamente** — uma conta criada sozinha usaria o sistema de graça.

Necessário:

- Criar o cliente/assinatura no financeiro no momento do cadastro (**depende de o financeiro ter API para
  isso — o código dele não está neste repositório**); **ou**
- Controlar um período de teste localmente (ex.: `empresas.trial_ate`) e bloquear ao vencer quando o cliente
  ainda não existir no financeiro;
- Aplicar a mesma verificação de assinatura na API `/api/v1` (hoje ela não passa por nenhuma).

### 2. Planos de verdade

`App\Models\Plano` está vazio (só registra observer) e os planos existem apenas como texto na landing page.
Criar uma tabela de planos com: limites (clientes, produtos, NFe, NFCe, NFS-e, MDFe), `cargo` liberado,
preço e se está disponível para cadastro público. O cadastro aplica o plano escolhido na empresa.

### 3. Fluxo em duas etapas

- **Etapa 1 — pública:** nome, e-mail, senha, CNPJ (autopreenche razão social/endereço) e plano. Cria
  empresa + usuário admin com padrões seguros: `ambiente = 2` (homologação), série 1, limites e cargo **do
  plano**, `tipo = admin`.
- **Etapa 2 — dentro do sistema:** assistente "configure sua emissão": certificado + senha, IE, CSC/IdCSC,
  CRT. Emissão bloqueada com mensagem clara até concluir.
- O `EmpresasController::store` atual exige certificado; o self-signup precisa de um fluxo próprio (no
  `EmpresasService`, reaproveitando `Empresa::salvar` / `UsersService::store`), não afrouxar a validação do
  cadastro do master.

### 4. Antiabuso

- Confirmação de e-mail: `MustVerifyEmail` está comentado em `App\Models\User`; exige SMTP configurado em
  produção (`.env.example` aponta para mailhog).
- `throttle` no endpoint de cadastro e captcha.
- Validar dígitos verificadores do CNPJ/CPF (hoje só há `unique:empresas,cpf_cnpj`).

### 5. Isolamento entre empresas

- A conta criada **nunca** pode receber `empresa_id = 1` (tenant master que enxerga todas as empresas —
  ver `CLAUDE.md`) nem os cargos `master`/`admin`.
- `cargo`, `tipo`, `empresa_id` e limites sempre definidos no servidor a partir do plano.

---

## Plano em fases

| Fase | Entrega | Porte |
|---|---|---|
| 0 | ~~Fechar o `/register` atual~~ ✅ feito em 24/09/2026 | minutos |
| 1 | Tabela de planos (limites + cargo) e período de teste local com bloqueio, na web e na API | pequeno/médio |
| 2 | Cadastro público (CNPJ, plano, e-mail confirmado, antiabuso) criando empresa + admin em homologação | médio |
| 3 | Assistente de configuração fiscal e bloqueio de emissão até concluir | médio |
| 4 | Integração com o financeiro (cliente/assinatura automáticos, cobrança via Pix) | depende da API do financeiro |

## Decisões em aberto

- [ ] O financeiro (`financeiro.f-softsistemas.com.br`) tem API para **criar** cliente e assinatura?
      Se não, começar com trial local e cadastrar a cobrança manualmente após o teste.
- [ ] Duração do teste grátis e o que ele libera (a landing promete "1ª nota fiscal de teste grátis").
- [ ] Quais planos entram no cadastro público e qual `cargo` cada um libera.
- [ ] Cadastro aceita CPF (MEI/produtor) ou só CNPJ?
- [ ] Após o teste: bloquear tudo, ou só a emissão (mantendo acesso a cadastros e XMLs)?

## Testes a prever

- Cadastro não aceita `cargo`/`tipo`/`empresa_id` da requisição (tentativa de `cargo=master`).
- Empresa criada em homologação, com limites e cargo do plano.
- CNPJ duplicado e CNPJ inválido rejeitados.
- Trial vencido bloqueia web **e** API.
- Emissão bloqueada sem certificado configurado.
