# Módulo Integration

Infraestrutura técnica para integrações externas. Projetado para ser multi-tenant futuramente (stancl/tenancy, schema-per-tenant), mas sem implementá-lo agora.

## Estrutura

- `Core/` - `AbstractHttpClient`, `IntegrationLogger`, `Support/` (mascaramento, helpers)
- `Contracts/` - Contratos técnicos (`IConnectionResolver`, `IWebhookHandler`)
- `Exceptions/` - Exceções técnicas (não estendem `ApiException`)
- `Enums/` - `IntegrationLogStatus`, `IntegrationDirection`, `WebhookEventStatus`
- `Models/` - `IntegrationConnection`, `IntegrationLog`, `WebhookEvent`
- `Repositories/` - Repositórios
- `Services/` - Serviços
- `Http/` - Controllers/Requests/Resources
- `Jobs/` - `ProcessWebhookEventJob`
- `Registry/` - `ProviderRegistry`
- `Providers/` - Adapters concretos de provedores (vazio por ora)

## Regras de dependência
- `Integration/Core` **NÃO** pode importar nada de módulos de domínio (Geography, Financial, ThirdParty, etc.).
- Módulos de domínio são donos de **contratos/DTOs** (interfaces). Adapters em `Integration/Providers/<Provedor>` implementarão esses contratos (fora do escopo atual).
- Toda resolução de credenciais deve passar por `IConnectionResolver` (ponto único que ficará tenant-aware no futuro).
- `IntegrationUnavailableApiException` (estende `ApiException`, HTTP 503, code `integration_unavailable`) é a exceção para serviços de domínio traduzirem falhas técnicas.

## Multi-tenancy (preparado, não implementado)
- Não há `tenant_id` nas tabelas. As tabelas `integration_connections`, `integration_logs`, `integration_webhook_events` são pensadas para viver **no schema do tenant** no futuro.
- Credenciais são armazenadas em `credentials` com cast `encrypted:array`, **nunca** retornadas nas respostas; somente via `IConnectionResolver`.
- Cache keys devem passar por helper único (hoje prefixa provedor; amanhã poderá incluir tenant).
- Jobs devem carregar `id/uuid` da conexão, **nunca** credenciais resolvidas.

## Como adicionar um provedor
1. Criar adapter em `Integration/Providers/<Provedor>/` implementando contrato do domínio.
2. Registrar o provedor no `ProviderRegistry` via ServiceProvider do módulo/provedor (chave, capacidades, factory).
3. Caso precise receber webhooks, implementar `IWebhookHandler` e registrá-lo no registry.
4. Usar `AbstractHttpClient` com timeout/retry configuráveis; garantir idempotência quando houver retry.
5. Registrar chamadas via `IntegrationLogger` (mascarar dados sensíveis).
6. Nunca acessar credenciais diretamente — usar `IConnectionResolver`.

## Tabelas
- `integration_connections` (uuid público, provider, name, credentials encrypted:array, settings json, is_active, last_used_at) — sem tenant_id
- `integration_logs` (provider, connection_id nullable, operation, direction, method, url_sanitized, status_code, duration_ms, request_masked/truncated, response_masked/truncated, status, error_class, correlation_id)
- `integration_webhook_events` (provider, connection_id nullable, external_id nullable com unique por provider, headers, payload bruto, status, attempts, processed_at, erro)
