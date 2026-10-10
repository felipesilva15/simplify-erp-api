# Simplify ERP API

API REST do Simplify ERP, implementada em Laravel 13. Concentra o acesso autenticado a dados de ERP para aplicações cliente (web/mobile), com controle de acesso por permissões, registro de auditoria e um conjunto de cadastros base — módulos, recursos, permissões, perfis, usuários, tipos de parceiro, parceiros e contatos.

O projeto é um **monólito**: uma única aplicação Laravel, publicada e implantada, sem microsserviços, filas de integração nem orquestração de containers no repositório.

| Documento | Conteúdo |
|---|---|
| [`docs/prd.md`](docs/prd.md) | O que o sistema é, por que existe, escopo, regras de negócio e requisitos. |
| [`docs/spec.md`](docs/spec.md) | Como o sistema está estruturado: arquitetura, contratos HTTP, autenticação, persistência, gerador de módulos e testes. |

---

## Stack

| Item | Versão / valor |
|---|---|
| PHP | `^8.4` (instalado: `8.4.25`) |
| Laravel Framework | `^13.0` (instalado: `v13.34.0`) |
| Autenticação | `tymon/jwt-auth` `^2.2` (JWT em cookie httpOnly) |
| Documentação OpenAPI | `darkaonline/l5-swagger` `^11.1` + `zircote/swagger-php` `6.11` + `scalar/laravel` `^0.4` |
| Exportação tabular | `maatwebsite/excel` `^4.0` |
| Persistência | MySQL (padrão em `.env.example`) ou PostgreSQL |
| Testes | PHPUnit `^12.5` |
| Front-end de apoio | Vite `^8`, Tailwind CSS `^4` (landing page e assets; a API não é SPA) |

---

## Pré-requisitos

- PHP `^8.4` — declarado no `composer.json` e refletido pelo `composer.lock` (instalado com PHP `8.4.25`). O repositório **não declara** requisitos de extensão no `composer.json`; a extensão de banco (`pdo_mysql` ou `pdo_pgsql`) precisa estar habilitada para as migrations.
- Composer 2.
- Node.js com npm, para o build do Vite/Tailwind. O `package.json` **não fixa** versão, mas o Vite 8 exige Node `^20.19.0 || >= 22.12.0`.
- Um banco relacional acessível — MySQL ou PostgreSQL, conforme `config/database.php` — com o banco criado. O `.env.example` assume `simplify_erp`:
  ```sql
  CREATE DATABASE simplify_erp;
  ```
  Para MySQL com `utf8mb4`: `CREATE DATABASE simplify_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`.
- Os arquivos `storage/data/cidades.csv` e `storage/data/cbo-ocupacao.csv`, versionados no repositório, são necessários apenas para os seeders de cidade e profissão.

---

## Instalação

Atalho com os scripts do `composer.json`:

```bash
composer setup
```

O script `setup` executa, em ordem: `composer install`; copia `.env.example` para `.env` (se ainda não existir); `php artisan key:generate`; `php artisan migrate --force`; `npm install`; `npm run build`.

Após o `composer setup` **ainda é preciso** gerar o segredo JWT e popular o banco, pois o script não os inclui:

```bash
php artisan jwt:secret          # define JWT_SECRET no .env
php artisan migrate --seed      # cria usuários, ACL e cadastros geográficos
```

Passo a passo equivalente, se preferir executar manualmente:

```bash
composer install
cp .env.example .env           # Windows: copy .env.example .env
php artisan key:generate
php artisan jwt:secret
npm install
npm run build
```

Depois de ajustar as variáveis de banco no `.env`:

```bash
php artisan migrate --seed
```

---

## Configuração

Todas as variáveis relevantes estão em [`.env.example`](.env.example). As que precisam de ajuste ou geração manual:

| Variável | Origem | Observação |
|---|---|---|
| `APP_KEY` | `php artisan key:generate` | gerada automaticamente. |
| `JWT_SECRET` | `php artisan jwt:secret` | **não está no `.env.example`**; obrigatória para assinar os tokens. |
| `DB_CONNECTION` | `.env.example` | `mysql` por padrão; o código também suporta `pgsql`. |
| `DB_DATABASE` | `.env.example` | `simplify_erp`. |
| `SESSION_DRIVER` / `CACHE_STORE` / `QUEUE_CONNECTION` | `.env.example` | `database` — exigem as tabelas criadas pelas migrations padrão do Laravel. |
| `JWT_TTL` / `JWT_REFRESH_TTL` | `.env.example` | `60` e `20160` minutos. O `JWT_TTL` também define a validade do cookie. |
| `JWT_COOKIE_NAME` | `.env.example` | `access_token`. |
| `JWT_COOKIE_SECURE` | `.env.example` | `true`; em desenvolvimento local sobre HTTP o cookie não será enviado pelo navegador. |
| `JWT_COOKIE_SAME_SITE` | `.env.example` | `lax`. |
| `L5_SWAGGER_GENERATE_ALWAYS` | `.env.example` | `true` — regenera a especificação OpenAPI a cada requisição. |
| `L5_SWAGGER_USE_ABSOLUTE_PATH` | `.env.example` | `false`. |
| `APP_LOCALE` / `APP_FALLBACK_LOCALE` | `.env.example` | `pt_BR` — define o idioma das mensagens de validação da API. |

---

## Rodando em desenvolvimento

Ambiente completo (servidor PHP, worker de fila e Vite) com um comando:

```bash
composer dev
```

Equivale a `php artisan serve`, `php artisan queue:listen --tries=1` e `npm run dev` em paralelo.

Somente a API:

```bash
php artisan serve          # http://localhost:8000
```

Verificação de saúde da aplicação: `GET /up`.

---

## Testes

```bash
composer test              # artisan config:clear && artisan test
php artisan test           # direto
php artisan test --filter=PartnerTest
```

A suíte roda sobre SQLite em memória (`phpunit.xml` define `DB_CONNECTION=sqlite` e `DB_DATABASE=:memory:`) e aplica `RefreshDatabase` na classe base, portanto **não exige banco externo**. Detalhes da estratégia de testes em [`docs/spec.md`](docs/spec.md#16-testes).

### Contrato de consulta

Todas as listagens, buscas rápidas e exportações compartilham o mesmo contrato:

| Parâmetro | Onde | Observação |
|---|---|---|
| `q` | `index`, `lookup`, `export` | Busca textual livre sobre as colunas pesquisáveis do recurso (definidas no repository, não pelo cliente). |
| `filters[coluna][operador]` | `index`, `export` | `eq`, `ne`, `lt`, `lte`, `gt`, `gte`, `like`. |
| `sorts` | `index`, `lookup`, `export` | CSV com `-` para decrescente, ex. `name,-created_at`. Padrão: `-id`. |
| `keys[]` | `lookup` | Coluna alvo configurável (`id` por padrão, `code` em tipos de parceiro). |
| `per_page` / `page` | `index`, `lookup`, `export` | `per_page` padrão 15 (30 no lookup); a exportação não pagina. |

Detalhes em [`docs/spec.md` §6](docs/spec.md#6-paginação-filtro-ordenação-e-busca-rápida).

---

## Documentação da API

| Recurso | URL | Observação |
|---|---|---|
| Swagger UI | `/api/documentation` | Título/versão definidos nas anotações `@OA\Info` do controller base. Rota pública. |
| Scalar (API Reference) | `/api-docs` | Rota definida em `routes/web.php`. |
| Especificação OpenAPI | `/docs?api-docs.json` | Gerada a partir das annotations `@OA` em `app/`. |
| Arquivo gerado | `storage/api-docs/api-docs.json` | Regerada automaticamente quando `L5_SWAGGER_GENERATE_ALWAYS=true`. |

Os tag groups da especificação (`Security`, `ThirdParty`, `HR`, `Geography`) são declarados no docblock de `app/Core/Http/Controllers/Controller.php` e são estendidos automaticamente pelo gerador de módulos.

> **Anotações `@OA` no l5-swagger 11.** O l5-swagger 11 passa a analisar apenas atributos PHP e descarta as anotações `@OA` em docblock. O projeto mantém os docblocks em funcionamento por meio de `App\Core\Services\SwaggerGeneratorFactory`, registrado em `AppServiceProvider`; as anotações estão depreciadas no swagger-php 6.11 e serão removidas no 8.0. Detalhes em [`docs/spec.md` §14](docs/spec.md#14-documentação-openapi).

---

## Gerando um módulo novo

O comando `make:module-crud` cria a estrutura completa de uma entidade (model, repository, interface, service, DTO, controller, requests, resources, policy, factory, testes) e registra as rotas, os bindings e o tag group do OpenAPI:

```bash
php artisan make:module-crud Core Vehicle --all
php artisan make:module-crud Sales Order --all --lookup --export
```

**A tabela da entidade precisa existir no banco antes da execução** — o comando lê o schema real da tabela para derivar `$fillable`, regras de validação, DTO, resources, factory e export. O repository gerado recebe os hooks `getListColumnsToFilter()` (e `getLookupColumnsToFilter()` com `--lookup`) com as colunas pesquisáveis; os casos de teste de lookup e exportação só são gerados com as respectivas flags. Contrato completo em [`docs/spec.md`](docs/spec.md#15-gerador-de-módulos).

---

## Estrutura do projeto

```text
app/
  Core/                  # camada interna (framework de CRUD, contratos e utilitários)
    DTO/  Enums/  Exceptions/  Exports/  Helpers/  OA/
    Http/                # controllers, requests e resources compartilhados
    Models/  Repositories/  Services/  Traits/
  Modules/
    Security/            # users, roles, permissions, modules, resources, autenticação JWT
    ThirdParty/          # partner-types, partners, contacts
    Geography/           # countries, states, cities
  Console/
    Commands/            # make:module-crud
    Stubs/               # 29 stubs usados pelo gerador
  Policies/              # 11 policies (uma por entidade)
  Providers/             # registro de bindings, morph map e macro de rotas
bootstrap/providers.php  # providers registrados
config/                  # configs do Laravel e dos pacotes (jwt, l5-swagger, scalar, cors…)
database/
  migrations/ seeders/ factories/
lang/pt_BR/              # traduções de validação e paginação
routes/                  # api.php (API), web.php (landing e Scalar), console.php
storage/data/            # cidades.csv e cbo-ocupacao.csv usados pelos seeders
tests/
  Feature/               # 366 testes de integração HTTP
  Unit/                  # 110 testes unitários
```

`App\Core` é a camada interna do produto e é tratada como parte fundamental da arquitetura: módulos de negócio são sempre escritos estendendo `BaseCrudService`, `BaseRepository`, `ApiResponse`, `HasActivityLogs` e `HasExcelExport`. O `BaseCrudService` expõe ganchos sobrescrevíveis — `beforeStore`/`beforeUpdate`/`beforeDelete` (regras que bloqueiam, via `BusinessRuleException`), `afterStore`/`afterUpdate`/`afterDelete` (dentro da transação), `canEdit`/`editWarnings` e `prepareData`. Detalhes em [`docs/spec.md`](docs/spec.md#2-camada-core).

---

## Definição de Pronto

O que precisa acompanhar qualquer mudança neste projeto — padrões verificados no repositório e regras de processo sugeridas, a confirmar — está em [`docs/spec.md` §20](docs/spec.md#20-entregáveis-e-definição-de-pronto).

---

## Licença

MIT — ver [`LICENSE`](LICENSE).
