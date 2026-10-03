# Instruções para agentes de IA

Este repositório é a **Simplify ERP API** (Laravel 13). Antes de alterar código, leia a documentação:

- [`README.md`](README.md) — instalação, configuração, comandos e estrutura do projeto.
- [`docs/prd.md`](docs/prd.md) — o que o sistema é, escopo e regras de negócio.
- [`docs/spec.md`](docs/spec.md) — arquitetura, contratos HTTP, persistência, testes e padrões.

## Definição de Pronto (resumo)

A versão completa, com a separação entre padrões verificados e regras de processo, está em [`docs/spec.md` §20](docs/spec.md#20-entregáveis-e-definição-de-pronto). Em resumo, qualquer mudança deve:

- seguir o contrato de resposta único (`App\Core\Traits\ApiResponse`) e a autorização por policy (`authorizeResource`);
- validar toda entrada do cliente com `FormRequest`, com mensagens em `lang/pt_BR`;
- documentar a rota de API com annotation `@OA` — verificado por `tests/Feature/UI/OpenApiConsistencyTest.php`;
- vir acompanhada de teste em `tests/Feature` ou `tests/Unit` (a suíte roda em SQLite em memória, sem serviço externo);
- registrar escritas via `ActivityLogService`;
- criar schema novo apenas por migration datada em `database/migrations`;
- atualizar `README.md`, `docs/prd.md` e `docs/spec.md` na mesma entrega em que a mudança alterar comportamento, contrato, configuração ou arquitetura.

Comandos úteis:

```bash
composer test                          # config:clear + php artisan test
vendor/bin/pint                        # formatação (execução manual)
php artisan make:module-crud {Módulo} {Entidade} --all
```

> Observação: as anotações `@OA` em docblock são mantidas por `App\Core\Services\SwaggerGeneratorFactory` (ver [`docs/spec.md` §14](docs/spec.md#14-documentação-openapi)); estão depreciadas no swagger-php 6.11 e migrar para atributos PHP é o caminho futuro.
