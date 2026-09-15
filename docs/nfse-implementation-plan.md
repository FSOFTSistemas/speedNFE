# Plano de Implementacao NFS-e Nacional

## Objetivo

Implementar emissao de NFS-e Nacional no SpeedNFE usando DPS, schemas XSD v1.01, APIs REST oficiais e eventos de NFS-e, mantendo o padrao atual de services, controllers, permissoes e armazenamento fiscal do projeto.

## Premissas

- Modulo inicial focado em homologacao/producao restrita.
- Permissao base: `client-NFSe`.
- Certificado A1 `.pfx` e senha ja cadastrados em `empresas` serao reaproveitados.
- NFS-e Nacional nao deve usar `NFePHP\NFe\Make`; o XML da DPS sera gerado por componente proprio com `DOMDocument`.
- A validacao local deve usar os XSDs versionados no projeto antes da transmissao.
- As tabelas de dominio dos anexos devem ser importadas por migrations/seeders ou comando artisan idempotente.

## Fase 1: Fundacao

- Adicionar campos de empresa:
  - `ultimaNFSe`
  - `ultimaDPS`
  - `serieNFSe`
  - `limNFSe`
  - `inscricao_municipal`, se o campo atual `rg_ie` nao for suficiente
- Criar tabelas principais:
  - `nfses`
  - `nfse_xmls`
  - `nfse_eventos`
- Criar tabelas de dominio:
  - `nfse_servicos_nacionais`
  - `nfse_nbs`
  - `nfse_ind_ops`
  - `nfse_regras_incidencia`
- Adicionar relacionamentos em `Empresa`, `Cliente` e novos models NFS-e.

## Fase 2: Cadastro Fiscal de Servicos

- Expandir `servicos` para uso por NFS-e:
  - `cTribNac`
  - `cTribMun`
  - `cNBS`
  - `cIndOp`
  - `cClassTrib`
  - dados de ISSQN
  - dados de PIS/COFINS
  - defaults IBS/CBS
- Ajustar telas/API de servicos para preencher os campos obrigatorios da DPS.
- Criar validacoes por tipo de servico e local de prestacao.

## Fase 3: Geracao e Validacao da DPS

- Criar `App\Services\NFSe\DpsXmlBuilder`.
- Criar `App\Services\NFSe\NFSeSigner`.
- Criar `App\Services\NFSe\NFSeSchemaValidator`.
- Gerar `DPS/infDPS` com:
  - emitente/prestador
  - tomador
  - servico
  - valores
  - tributacao municipal/federal
  - grupo IBS/CBS quando exigido
- Validar XML contra `DPS_v1.01.xsd`.
- Criar testes unitarios com XML minimo valido.

## Fase 4: Transmissao REST

- Criar `App\Services\NFSe\NFSeClient` usando Guzzle.
- Configurar endpoints por ambiente:
  - producao restrita
  - producao
- Implementar envio de DPS para gerar NFS-e.
- Persistir:
  - XML DPS enviado
  - XML NFS-e retornado
  - chave
  - numero
  - protocolo/status
  - mensagem de rejeicao
- Criar tratamento de erros fiscais semelhante a `NFeErroUtil`.

## Fase 5: Fluxo Web/API

- Criar `NFSeController`.
- Criar rotas web protegidas por `client-NFSe`.
- Criar endpoints API v1 se o frontend separado precisar consumir.
- Implementar:
  - listar NFS-e
  - criar/editar rascunho
  - transmitir
  - visualizar
  - baixar XML
  - cancelar
- Adicionar menu apenas quando as rotas/telas estiverem funcionais.

## Fase 6: Eventos

- Implementar `pedRegEvento`.
- Prioridade:
  - cancelamento
  - cancelamento por substituicao
  - manifestacao/rejeicao quando aplicavel
- Validar contra `pedRegEvento_v1.01.xsd` e `evento_v1.01.xsd`.
- Guardar sequencia de eventos e XML de retorno.

## Fase 7: Homologacao e Producao

- Rodar cenarios em producao restrita.
- Validar municipios conveniados e parametros municipais.
- Conferir obrigatoriedade IBS/CBS conforme cronograma vigente.
- Habilitar producao por empresa somente apos teste de transmissao, cancelamento e download XML.

## Riscos

- Documentacao RTC/NFS-e esta em evolucao e pode mudar schemas/regras.
- Regras de incidencia municipal e IBS/CBS exigem tabela de dominio confiavel.
- A API REST pode exigir detalhes de autenticacao/certificado diferentes dos services NFe atuais.
- DANFSe pode depender de API oficial ou layout proprio, conforme disponibilidade do ambiente.

## Primeiro MVP

1. Permissao `client-NFSe`.
2. Tabelas `nfses`, `nfse_xmls` e campos de numeracao em `empresas`.
3. Importacao dos anexos B e C.
4. Geracao de DPS minima para prestador emitindo para tomador nacional.
5. Validacao XSD local.
6. Envio em producao restrita.
7. Armazenamento do XML autorizado e rejeicoes.
