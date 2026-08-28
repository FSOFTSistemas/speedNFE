# Deploy, implantação e teste da NFS-e Nacional

Este é o roteiro operacional completo para publicar a NFS-e Nacional. Execute os passos na ordem. Os comandos de migration estão separados de propósito e **não devem ser substituídos por `php artisan migrate` neste deploy**.

> Atenção: com `ambiente = 1`, a emissão gera um documento fiscal real. Use uma prestação real e dados reais. Para testes fictícios, use produção restrita (`ambiente = 2`).

## 1. Informações que precisam estar em mãos

Antes da janela de deploy, anote:

- diretório da aplicação em produção;
- comando usado para trocar para a nova versão/release;
- `ID_DA_EMPRESA` emissora;
- `ID_DO_SERVICO` que será usado no diagnóstico;
- usuário que receberá a permissão `client-NFSe`;
- CNPJ, inscrição municipal, código IBGE, série e último número de DPS;
- senha do certificado, sem registrá-la neste arquivo ou no Git;
- contato responsável por confirmar a nota no Portal Nacional.

## 2. Antes do deploy

1. Faça backup do banco e da versão atual da aplicação.
2. Confirme e preserve o `APP_KEY` atual; ele protege a senha e o conteúdo dos certificados no banco. Trocar essa chave torna os dados criptografados ilegíveis.
3. Confirme que o artefato contém:
   - `resources/schemas/nfse/v1.01/`
   - `resources/domains/nfse/v1.01/`
4. Verifique espaço em disco e conexão com o banco.
5. Confirme que `.env`, certificados, chaves e credenciais não fazem parte do artefato.
6. Não apague os certificados antigos antes do passo de importação e validação no banco.
7. Registre a versão/commit atual para permitir retorno do código se necessário:

```bash
git rev-parse HEAD
```

## 3. Colocar a aplicação em manutenção

Na janela de deploy:

```bash
php artisan down --retry=60
```

Se o servidor usa balanceador ou múltiplas instâncias, retire ou coloque em manutenção todas elas antes das migrations.

## 4. Publicar código e dependências

Depois de disponibilizar os arquivos da nova versão no servidor, execute dentro do diretório da aplicação:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
```

Se os assets não forem construídos pelo pipeline e não vierem prontos no artefato:

```bash
npm ci
npm run build
```

Garanta que o usuário do PHP tenha acesso de escrita somente aos diretórios necessários, como `storage/` e `bootstrap/cache/`.

## 5. Variáveis de produção

Configure no ambiente de produção, sem versionar o `.env`:

```dotenv
NFSE_LAYOUT_VERSION=1.01
NFSE_VER_APLIC=SpeedNFE-1.0
NFSE_SEFIN_RESTRITA_URL=https://sefin.producaorestrita.nfse.gov.br/API/SefinNacional
NFSE_SEFIN_PRODUCAO_URL=https://sefin.nfse.gov.br/SefinNacional
NFSE_SEFIN_TIMEOUT=30
NFSE_SEFIN_CONNECT_TIMEOUT=10
NFSE_DANFSE_URL=https://adn.nfse.gov.br
```

Também confira no `.env` existente:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://DOMINIO_DA_APLICACAO
```

Preserve o `APP_KEY` que já está em produção. **Não execute `php artisan key:generate`** em uma instalação existente, pois o conteúdo criptografado do certificado depende dessa chave.

Depois de alterar o ambiente, limpe caches antigos. O novo cache de configuração será criado depois das migrations:

```bash
php artisan optimize:clear
```

## 6. Migrations separadas

Execute **uma migration por vez**, exatamente nesta ordem. Só avance quando o comando anterior terminar sem erro.

```bash
php artisan migrate --path=database/migrations/2026_08_24_000000_add_client_nfse_permission.php --force
```

```bash
php artisan migrate --path=database/migrations/2026_08_24_010000_add_nfse_fields_to_empresas_table.php --force
```

```bash
php artisan migrate --path=database/migrations/2026_08_24_011000_create_nfses_tables.php --force
```

```bash
php artisan migrate --path=database/migrations/2026_08_24_020000_create_nfse_domain_tables.php --force
```

```bash
php artisan migrate --path=database/migrations/2026_08_24_030000_add_nfse_fields_to_servicos_table.php --force
```

```bash
php artisan migrate --path=database/migrations/2026_08_27_010000_add_certificado_conteudo_to_empresas_table.php --force
```

```bash
php artisan migrate --path=database/migrations/2026_08_27_020000_add_ibscbs_fields_to_nfses_table.php --force
```

Confira o status depois de concluir as sete:

```bash
php artisan migrate:status
```

As sete migrations devem aparecer como `Ran`. Não execute `migrate:rollback` dessas tabelas depois de começar a emitir documentos fiscais.

## 7. Domínios da NFS-e

Importe os códigos nacionais de serviço, NBS, indicadores de operação e regras de incidência:

```bash
php artisan nfse:importar-dominios
```

O importador é idempotente e pode ser executado novamente.

## 8. Certificados no banco

Primeiro importe e valide o certificado da empresa sem apagar o arquivo legado. Substitua `ID_DA_EMPRESA`:

```bash
php artisan certificados:importar-banco --empresa=ID_DA_EMPRESA
```

O comando abre o PFX com a senha cadastrada antes de gravá-lo criptografado. Corrija qualquer falha antes de continuar.

Após publicar esta versão, validar a aplicação e confirmar que o certificado está no banco, remova somente o arquivo legado dessa empresa:

```bash
php artisan certificados:importar-banco --empresa=ID_DA_EMPRESA --delete-files
```

Repita por empresa. Não remova a pasta inteira manualmente antes de verificar todas as empresas. Mantenha um backup protegido do certificado de acordo com a política da empresa.

## 9. Permissão e cadastro da empresa

1. Atribua `client-NFSe` ao usuário/cargo que emitirá NFS-e.
2. Na empresa emissora, confira:
   - CNPJ;
   - inscrição municipal;
   - município e código IBGE;
   - regime tributário;
   - série da NFS-e/DPS;
   - último número de DPS já utilizado;
   - limite de NFS-e;
   - ambiente `1` para produção ou `2` para produção restrita;
   - certificado e senha válidos.
3. O último número da DPS deve estar alinhado com o histórico do emissor nacional para evitar duplicidade.

## 10. Cadastro do serviço

Antes da emissão, confira no serviço:

- código nacional do serviço (`cTribNac`);
- código municipal, quando exigido;
- NBS, quando aplicável;
- indicador da operação (`cIndOp`);
- CST do IBS/CBS;
- classificação tributária do IBS/CBS (`cClassTrib`);
- tributação e retenção do ISSQN;
- alíquota do ISSQN;
- finalidade, consumidor final e destinatário.

## 11. Gerar cache e reiniciar processos

Depois das migrations, domínios e importação do certificado:

```bash
php artisan config:cache
```

Se houver workers de fila em execução:

```bash
php artisan queue:restart
```

Não inclua `php artisan view:cache` neste deploy até corrigir o componente Blade preexistente `guest-layout` da tela de redefinição de senha.

## 12. Diagnóstico e testes antes de liberar o acesso

Execute o diagnóstico sem transmitir documentos, substituindo os IDs:

```bash
php artisan nfse:diagnostico --empresa=ID_DA_EMPRESA --servico=ID_DO_SERVICO
```

Execute primeiro os testes específicos:

```bash
php artisan test --filter='NFSeXmlTest|NFSeClientTest|NFSeMigrationTest|EmpresaCertificateTest'
```

Depois execute a suíte completa:

```bash
php artisan test
```

Também confira as rotas:

```bash
php artisan route:list --path=nfse
```

Se qualquer comando falhar, mantenha a aplicação em manutenção e corrija o problema antes de avançar.

## 13. Liberar a aplicação

Quando migrations, diagnóstico e testes estiverem aprovados:

```bash
php artisan up
```

Faça login com um usuário autorizado e confirme que a listagem e o formulário de NFS-e abrem sem erro antes de transmitir.

## 14. Teste controlado de emissão

1. Cadastre ou selecione um tomador real.
2. Selecione um serviço com todos os dados fiscais do item anterior.
3. Crie a NFS-e como rascunho e revise valores, competência, município e IBS/CBS.
4. Clique em **Enviar** apenas uma vez.
5. Se aparecer “transmissão inconclusiva”, não reenvie. Use **Sincronizar com a SEFIN** até recuperar a resposta.
6. Após autorização, confirme:
   - situação `Autorizado`;
   - chave de acesso e número da NFS-e;
   - XML autorizado armazenado no banco;
   - download do XML;
   - download do DANFSe;
   - existência da nota no portal nacional.
7. Confira os valores de IBS/CBS retornados no XML autorizado.

## 15. Teste de cancelamento

Faça este teste somente em uma nota que realmente deva ser cancelada:

1. Abra a NFS-e autorizada.
2. Escolha o motivo oficial `1`, `2` ou `9`.
3. Informe justificativa real entre 15 e 255 caracteres.
4. Confirme o cancelamento.
5. A situação local só deve mudar para `Cancelado` depois do aceite da SEFIN.
6. Confira o evento no portal nacional e o XML do evento em `nfse_eventos`.

## 16. Verificação final

- Não há PFX exposto em diretório público.
- `APP_DEBUG=false` em produção.
- HTTPS ativo.
- Logs da aplicação sem conteúdo de certificado ou senha.
- Backup confirmado.
- Migrations marcadas como executadas.
- Domínios importados.
- Usuário com permissão `client-NFSe`.
- Emissão, sincronização, XML, DANFSe e cancelamento verificados.
- Workers de fila reiniciados, quando usados.
- Aplicação retirada do modo de manutenção.
- Logs acompanhados durante a primeira emissão.

Para acompanhar erros durante o teste, use o mecanismo de logs adotado pelo servidor. Em uma instalação padrão do Laravel:

```bash
tail -f storage/logs/laravel.log
```

Interrompa o `tail` com `Ctrl+C` depois da verificação.

## 17. Plano de retorno em caso de falha

Antes de qualquer emissão fiscal, se o deploy falhar:

1. mantenha a aplicação em manutenção;
2. guarde os logs e identifique qual etapa falhou;
3. restaure a versão anterior do código pelo procedimento de releases do servidor;
4. restaure o backup do banco se alguma migration parcialmente aplicada não puder ser corrigida com segurança;
5. limpe/recrie o cache de configuração e só então retire a manutenção.

Depois que houver NFS-e emitida, **não reverta migrations nem restaure um backup antigo por conta própria**, pois isso pode apagar o vínculo entre a aplicação e documentos fiscais autorizados. Corrija avançando com uma nova versão ou restaure somente com um plano fiscal e de banco validado.

## 18. Resumo copiável da ordem

1. Backup do banco, aplicação, `.env`, `APP_KEY` e certificados.
2. `php artisan down --retry=60`.
3. Publicar código e executar `composer install`.
4. Conferir `.env` e executar `php artisan optimize:clear`.
5. Executar as sete migrations individualmente, na ordem da seção 6.
6. Executar `php artisan migrate:status`.
7. Executar `php artisan nfse:importar-dominios`.
8. Importar o certificado sem apagar o legado.
9. Conferir permissão, empresa, numeração e serviço.
10. Executar `php artisan config:cache` e reiniciar filas, se usadas.
11. Executar diagnóstico, testes específicos, suíte completa e conferir rotas.
12. Executar `php artisan up`.
13. Abrir a NFS-e, fazer uma emissão real controlada e sincronizar se necessário.
14. Validar XML, DANFSe e Portal Nacional.
15. Apagar o certificado legado somente depois da validação pelo banco.
16. Testar cancelamento apenas se a nota realmente precisar ser cancelada.
