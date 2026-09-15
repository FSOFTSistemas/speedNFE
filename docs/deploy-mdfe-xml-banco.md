# Deploy do XML de MDF-e no banco

Este runbook registra a implantação deliberada e separada da migration que move a persistência dos XMLs de MDF-e para o banco de dados.

Execute os comandos a partir da raiz da aplicação Laravel. Não apague `public/xml_mdfe` antes de concluir todas as verificações deste documento.

## Arquivos que precisam estar no release

- `database/migrations/2026_08_27_000000_create_mdfe_xmls_table.php`
- `app/Models/MDFeXml.php`
- `app/Console/Commands/BackfillMDFeXmls.php`
- alterações de MDF-e em `app/Models/MDFE.php`, `app/Services/MDFeService.php`, `app/Services/NotasService.php` e `app/Http/Controllers/MDFEController.php`

## 1. Antes da implantação

1. Faça backup completo do banco de dados.
2. Faça backup de `public/xml_mdfe` fora da raiz pública do servidor.
3. Confirme que o `APP_KEY` de produção está preservado e não execute `php artisan key:generate`.
4. Confirme que o release novo já está disponível no servidor.

## 2. Colocar a aplicação em manutenção

```bash
php artisan down
```

Mantenha a aplicação em manutenção até terminar o backfill e os testes de leitura.

## 3. Conferir a migration separada

```bash
php artisan migrate:status --path=database/migrations/2026_08_27_000000_create_mdfe_xmls_table.php
```

Antes de executar, é possível conferir o SQL sem alterar o banco:

```bash
php artisan migrate --path=database/migrations/2026_08_27_000000_create_mdfe_xmls_table.php --pretend --force
```

## 4. Executar somente a migration do MDF-e

```bash
php artisan migrate --path=database/migrations/2026_08_27_000000_create_mdfe_xmls_table.php --force
```

Confirme que ela aparece como executada:

```bash
php artisan migrate:status --path=database/migrations/2026_08_27_000000_create_mdfe_xmls_table.php
```

## 5. Simular o backfill dos XMLs antigos

```bash
php artisan mdfe:backfill-xml --dry-run
```

Confira no resumo:

- quantidade de arquivos encontrados;
- quantidade pronta para gravação;
- quantidade sem MDF-e correspondente.

Se `Sem MDF-e correspondente` for maior que zero, interrompa a implantação e não apague nenhum XML. Investigue as chaves que não foram associadas.

No ambiente de desenvolvimento usado na implementação existem 394 XMLs de MDF-e: 202 autorizados, 188 encerrados e 4 cancelados. Os números de produção podem ser diferentes.

## 6. Importar os XMLs para o banco

```bash
php artisan mdfe:backfill-xml
```

O comando usa `updateOrCreate`, portanto é idempotente e pode ser executado novamente sem duplicar a combinação de MDF-e e tipo de XML.

## 7. Validar o conteúdo importado

Abra o Tinker:

```bash
php artisan tinker
```

Consulte a quantidade agrupada por tipo:

```php
App\Models\MDFeXml::query()
    ->selectRaw('tipo, COUNT(*) as total')
    ->groupBy('tipo')
    ->pluck('total', 'tipo');
```

Confira também quantos arquivos antigos existem:

```bash
find public/xml_mdfe -type f -name '*.xml' | wc -l
```

Um mesmo MDF-e pode possuir mais de um XML, por exemplo um autorizado e outro encerrado. Por isso a comparação final deve ser feita por tipo, não apenas pela quantidade de MDF-es.

## 8. Testes obrigatórios antes de liberar

Teste pela interface, usando registros já existentes:

1. download de XML autorizado;
2. impressão de DAMDFE autorizado;
3. download e impressão de MDF-e encerrado;
4. download e impressão de MDF-e cancelado;
5. emissão de um novo MDF-e em homologação, se houver ambiente seguro disponível.

Os downloads e impressões devem funcionar com os dados da tabela `mdfe_xmls`, mesmo que os arquivos antigos estejam temporariamente indisponíveis para o servidor web.

## 9. Recriar caches e liberar a aplicação

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan up
```

## 10. Limpeza posterior

Não remova `public/xml_mdfe` no mesmo momento da primeira implantação. Mantenha o backup até confirmar o funcionamento em produção e a política de retenção fiscal.

A exclusão dos XMLs antigos deve ocorrer em uma etapa posterior, depois que:

- o backfill terminar sem registros não associados;
- as contagens por tipo forem conferidas;
- os testes de download e impressão passarem;
- o backup estiver validado e armazenado fora da raiz pública.

Não use rollback desta migration depois do backfill, pois isso excluiria a tabela `mdfe_xmls` e os XMLs importados. Em caso de falha após a importação, restaure o backup ou corrija o release mantendo a tabela e seus dados.
