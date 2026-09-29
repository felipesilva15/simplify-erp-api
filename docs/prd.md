# PRD — Simplify ERP API

> Visão de produto e de negócio. A especificação técnica complementar está em [`spec.md`](spec.md); os comandos de execução, em [`../README.md`](../README.md). Nenhuma regra aqui é duplicada no `spec.md` e vice-versa.

---

## 1. Propósito

A **Simplify ERP API** é a camada de serviço do Simplify ERP. Ela centraliza o acesso autenticado a dados de ERP para aplicações cliente (web e mobile), garantindo que toda leitura e escrita passe por um único contrato: autenticação por token, autorização por permissão, validação declarada, resposta padronizada e trilha de auditoria.

Não é um ERP completo: é a API que o ERP e seus módulos consomem.

## 2. Problema resolvido

Sem esta camada, cada front-end do Simplify ERP implementaria por conta própria:

- **controle de acesso** — decidir quem pode ver e alterar cada registro;
- **regras de consistência** — quais campos são obrigatórios conforme o tipo de pessoa, como contatos são sincronizados, como nomes de permissão são derivados;
- **auditoria** — quem criou, alterou ou excluiu cada registro;
- **contrato de resposta** — paginação, ordenação, filtro, exportação e formato de erro.

O problema é a dispersão dessas decisões e a consequente inconsistência entre telas. A API as centraliza em uma camada interna (`App\Core`) reutilizada por todos os módulos.

## 3. Contexto e consumidores

| Consumidor | Canal | Observação observada no código |
|---|---|---|
| Aplicação web do ERP | REST `/api/*` | `config/cors.php` libera `http://localhost:4200` com `supports_credentials: true`, o que indica um cliente SPA com cookies. |
| Aplicação mobile do ERP | REST `/api/*` | Não há rota mobile específica no repositório. |
| Integrações / scripts | `GET /api/test` | Rota de depuração que devolve todos os registros de auditoria. Sem autenticação — ver [§11](#11-observações-do-código-não-determinados). |

O único consumidor com rota nominal própria no repositório é o portal de documentação (Swagger UI e Scalar), que é público.

---

## 4. Objetivos

1. Oferecer um contrato REST único e previsível para os dados de ERP.
2. Garantir que toda operação autenticada seja autorizada por permissão explícita, exceto para administradores.
3. Manter registro de auditoria de toda escrita realizada pela API.
4. Tornar a criação de um novo módulo de negócio um processo padronizado e repetível.
5. Manter a especificação OpenAPI gerada a partir do próprio código, sem divergência manual.

## 5. Escopo

### 5.1 Dentro do escopo (implementado)

| Domínio | Capacidades |
|---|---|
| **Autenticação** | Login por cookie httpOnly, emissão de token, renovação, logout, identificação do usuário logado. |
| **Controle de acesso** | Módulos, recursos, permissões, perfis (roles) e seus vínculos; administradores com acesso total. |
| **Usuários** | CRUD, busca rápida (lookup), exportação, vínculo com perfis. |
| **Cadastros geográficos** | Consulta de países, estados e cidades e busca rápida em cada um. |
| **Parceiros** | CRUD de parceiros (pessoa física, empresa ou estrangeiro) com contatos aninhados; busca rápida; exportação. |
| **Tipos de parceiro** | CRUD e busca rápida. |
| **Auditoria** | Listagem do histórico de um registro por recurso, com autor, ação, rota, IP e user agent. |
| **Infraestrutura de API** | Filtro, ordenação, paginação, lookup, exportação tabular, envelope de resposta, erros padronizados, documentação OpenAPI. |
| **Geração de módulos** | Comando que produz a estrutura completa de uma entidade e a registra no projeto. |

### 5.2 Fora do escopo no código atual (observado, não planejado)

Os itens abaixo existem como estrutura de dados ou configuração **sem** rota, modelo ou fluxo próprio. Estão registrados aqui como fato, não como intenção.

- Tabela `addresses` (migration `2026_09_08_004332`) — sem model, sem repository, sem rota.
- Tabela `professions` (migration `2026_09_23_212455`) — populada por seeder, mas sem model e sem rota.
- Integração com serviços de terceiros, mensageria, e-mail ou webhooks: nenhuma existe no código.

---

## 6. Casos de uso

| # | Caso de uso | Atores |
|---|---|---|
| UC-01 | Autenticar-se e obter sessão | Cliente da API |
| UC-02 | Renovar o token antes do vencimento | Cliente da API |
| UC-03 | Encerrar a sessão | Cliente da API |
| UC-04 | Obter os dados do usuário autenticado | Cliente da API |
| UC-05 | Gerenciar usuários, vinculando-os a perfis | Administrador / usuário com permissão |
| UC-06 | Gerenciar perfis e definir suas permissões | Administrador / usuário com permissão |
| UC-07 | Gerenciar permissões de um recurso | Administrador / usuário com permissão |
| UC-08 | Gerenciar módulos e os recursos que os compõem | Administrador / usuário com permissão |
| UC-09 | Consultar países, estados e cidades | Qualquer usuário autenticado com permissão |
| UC-10 | Buscar registros para preencher campos de seleção (lookup) | Qualquer usuário autenticado com permissão |
| UC-11 | Cadastrar e manter parceiros com seus contatos | Administrador / usuário com permissão |
| UC-12 | Exportar usuários, perfis ou parceiros para planilha | Usuário com permissão de exportação |
| UC-13 | Auditar as alterações de um registro | Usuário com permissão de leitura |
| UC-14 | Gerar um novo módulo de negócio | Desenvolvedor |

---

## 7. Funcionalidades e regras de negócio

### 7.1 Autenticação e sessão

- O login exige `username` e `password`; a validação ocorre contra o campo `username` do usuário, não contra o e-mail.
- Existem duas formas de obter um token:
  - **por cookie** — `POST /api/security/auth/login` grava o token em cookie criptografado, httpOnly, com a mesma validade do token, e responde `204` sem corpo;
  - **por corpo** — `POST /api/security/auth/token` responde `200` com o token no corpo.
- O token é assinado com `JWT_SECRET`, tem validade de `JWT_TTL` minutos e pode ser renovado até `JWT_REFRESH_TTL` minutos.
- O cookie tem **precedência absoluta** sobre o header `Authorization`: o middleware de requisição remove o header recebido e o reescreve a partir do cookie. Uma requisição autenticada apenas por header, sem cookie, **não é autenticada**.
- Logout, login e renovação são registrados na auditoria com a ação `AUTH`.
- O nome do cookie usado no logout é fixo no código (`token`), independentemente de `JWT_COOKIE_NAME` — ver [§11](#11-observações-do-código-não-determinados).

### 7.2 Controle de acesso

- A permissão de uma ação é a string `{slug-do-recurso}.{ação}`, por exemplo `users.viewAny`, `roles.definePermissions`, `partners.export`.
- As ações verificáveis por recurso são `viewAny`, `view`, `create`, `update`, `delete` e, quando aplicável, `export`; `roles` acrescenta `definePermissions`.
- O nome da permissão **não é informado pelo cliente**: é sempre derivado do recurso e da ação (ver [spec.md](spec.md#9-permissões-e-policies)).
- Usuários com `is_admin = true` têm acesso a tudo, independentemente das permissões vinculadas.
- Módulos podem ser marcados como inativos; nesse caso a edição de um registro do módulo sinaliza `editable: false` e um aviso na resposta, mas a edição continua sendo aceita pela API.

### 7.3 Usuários

- Cadastro exige nome, e-mail (único), senha, `username` (único) e o campo `is_admin`; telefone e perfis são opcionais.
- A senha é armazenada com hash e nunca retornada pela API.
- A atualização é total: exige todos os campos de negócio, exceto a senha, que não é alterável por essa rota.
- A lista exibe as permissões efetivas do usuário; para administradores, o valor é `["*"]`.
- Perfis vinculados podem ser enviados já no cadastro e na alteração.
- A busca rápida (`lookup`) filtra por `id`, nome, e-mail e `username`.

### 7.4 Parceiros e contatos

- Um parceiro pertence a um **tipo de parceiro** identificado por `code` (não por `id`) e pode ser **pessoa física**, **empresa** ou **estrangeiro**.
- Campos de pessoa jurídica (documento de identidade, emitente, estado civil, gênero, dados dos pais) **não podem** ser enviados quando `person_type = COMPANY`.
- Campos cadastrais (inscrições estadual, municipal e SUFRAMA) **não podem** ser enviados quando `person_type = PERSON`.
- Quando `person_type = PERSON`, qualquer valor enviado para `taxpayer_type` é **sobrescrito no servidor** com `EXEMPT` (isento). O cliente não consegue gravar outro valor para esse caso.
- Quando `taxpayer_type = TAXPAYER`, a inscrição estadual passa a ser obrigatória.
- Gênero e data de nascimento são obrigatórios apenas para pessoa física.
- O `document_number` deve ser único entre os parceiros; não há validação de dígitos verificadores de CPF/CNPJ.
- Os contatos são enviados **dentro do próprio parceiro**, no campo `contacts`, e sincronizados na mesma transação:
  - item sem `id` é criado; item com `id` é atualizado; item existente **ausente** da lista é excluído (soft delete); item reenviado que estava excluído é restaurado;
  - um `id` que não pertence ao parceiro informado é rejeitado com `422`;
  - a resposta traz `meta.children.contacts` com as contagens `created`, `updated` e `deleted`;
  - omitir a chave `contacts` **preserva** os contatos existentes; enviar lista vazia **remove** todos.
- Regras dos contatos: cada contato precisa de **ao menos um** meio de contato (`email`, `mobile` ou `phone`) e **no máximo um** contato por parceiro pode ser marcado como principal.
- Falha em qualquer contato **desfaz** toda a gravação do parceiro.

### 7.5 Auditoria

- Toda criação, alteração e exclusão pela API gera um registro contendo: recurso afetado, identificador, ação, usuário autenticado, descrição, nome e caminho da rota, IP e user agent.
- Login, logout e renovação de token também são registrados.
- A descrição padrão deriva da ação ("Registro criado", "Registro alterado", "Registro excluído"); operações sem descrição explícita usam esse texto.
- O histórico é consultável por recurso em `GET /api/{módulo}/{recurso}/{id}/activity-logs`, respeitando paginação, ordenação e a permissão de leitura do recurso.

### 7.6 Consultas, busca rápida e exportação

- Todas as listagens suportam **filtro por campo e operador**, **ordenação** e **paginação**; o contrato de parâmetros é único para todos os recursos.
- A busca rápida (`lookup`) é um endpoint enxuto, orientado a preencher campos de seleção, e devolve `key`, `label`, `sublabel` e `meta`.
- A exportação gera planilha a partir da **mesma** consulta da listagem, respeitando filtros e ordenação, e exige permissão de exportação específica do recurso. Somente usuários, perfis e parceiros possuem exportação.

### 7.7 Cadastros geográficos

- Países, estados e cidades são **somente leitura** pela API e não usam soft delete.
- Estados referenciam o país por chave estrangeira; cidades referenciam o estado por chave estrangeira.
- A carga inicial é feita por seeders: Brasil e as 27 unidades federativas de forma embutida, e as cidades importadas do IBGE a partir de `storage/data/cidades.csv`.

---

## 8. Requisitos funcionais

| ID | Requisito |
|---|---|
| RF-01 | Autenticar usuário por `username` e `password` e retornar token, por cookie ou por corpo. |
| RF-02 | Renovar, invalidar e inspecionar a sessão do usuário autenticado. |
| RF-03 | Manter usuários, vinculando-os a zero ou mais perfis. |
| RF-04 | Manter perfis e suas permissões, inclusive a definição de permissões por operação dedicada. |
| RF-05 | Manter permissões, derivando o nome da permissão a partir do recurso e da ação. |
| RF-06 | Manter módulos e seus recursos. |
| RF-07 | Consultar países, estados e cidades, com listagem e busca rápida. |
| RF-08 | Manter tipos de parceiro, com listagem e busca rápida. |
| RF-09 | Manter parceiros com contatos aninhados, com listagem, busca rápida e exportação. |
| RF-10 | Consultar o histórico de auditoria de qualquer registro de recurso. |
| RF-11 | Filtrar, ordenar e paginar qualquer listagem por um contrato único. |
| RF-12 | Exportar usuários, perfis e parceiros em planilha respeitando os filtros da listagem. |
| RF-13 | Gerar, a partir do schema do banco, a estrutura completa de um novo módulo de negócio. |
| RF-14 | Publicar a especificação OpenAPI da API gerada a partir do código. |

---

## 9. Requisitos não funcionais

| ID | Requisito | Observação observada no código |
|---|---|---|
| RNF-01 | Todas as mensagens retornadas pela API devem estar em português. | Locale `pt_BR`; traduções em `lang/pt_BR`; labels de enums em português. |
| RNF-02 | A resposta da API deve ter um envelope único e estável. | Contrato único em `App\Core\Traits\ApiResponse`. |
| RNF-03 | O código-fonte (métodos, funções, variáveis, tabelas) deve estar em inglês. | Convenção observada de forma consistente em `app/`. |
| RNF-04 | Falhas de validação e de regra de negócio devem ser distinguíveis e localizadas no registro afetado. | `422` com `errors` indexado por campo ou por caminho de item aninhado. |
| RNF-05 | A especificação OpenAPI deve corresponder às rotas registradas. | Verificado por teste automatizado de consistência entre documentação e rotas. |
| RNF-06 | Escritas com itens aninhados devem ser atômicas. | Parceiro e contatos são gravados na mesma transação. |
| RNF-07 | Exclusões de dados de negócio devem preservar o histórico. | Soft delete nos recursos de negócio; auditoria registra a exclusão. |
| RNF-08 | A suíte de testes deve rodar sem dependência de serviços externos. | SQLite em memória; nenhum `Http::fake`, `Queue::fake` ou serviço real mockado além do Excel. |

---

## 10. Restrições e premissas

**Restrições observadas no código:**

- A API só é acessível por **cookie**: o middleware de requisição descarta o header `Authorization` recebido do cliente. Clientes que não suportem cookie precisam consumir `POST /security/auth/token` e reenviar o token por cookie.
- `config/cors.php` libera apenas `http://localhost:4200`; outras origens exigem alteração de configuração.
- A documentação da API (Swagger UI, Scalar e o JSON de especificação) é **pública** — `config/l5-swagger.php` não define middleware.
- Os dados de país/estado/cidade e de ocupação só entram por seeder; não há importação pela API.
- A especificação OpenAPI é gerada por annotation dentro dos controllers, com `L5_SWAGGER_GENERATE_ALWAYS=true` em desenvolvimento.

---

**Premissas:**

- Aplicações cliente consomem a API já autenticadas por cookie e tratam o envelope de resposta como contrato estável.
- A criação de módulos segue o comando `make:module-crud` e a tabela alvo já existe quando o comando é executado.
- O banco é gerenciado por migrations; não há fluxo de versionamento de dados além dos seeders.

## 11. Observações do código (não determinados)

Fatos observados que não constituem requisito e não têm confirmação no código de que sejam intencionais. Listados aqui para que não sejam interpretados como comportamento desejado:

1. `GET /api/test` está registrada fora do grupo de autenticação e devolve **todos** os registros de auditoria sem exigir token. É excluída da especificação OpenAPI por teste, mas permanece registrada.
2. O seeder de ACL cria 25 permissões, porém **não cria perfis** nem os vínculos `role_user` / `permission_role`. Em uma instalação recém-populada, o acesso administrativo depende exclusivamente de `users.is_admin`.
3. A relação de perfis para usuários (`Role::users()`) referencia o pivô `PermissionRole` em vez de `RoleUser`; as duas tabelas existem e são distintas.
4. `config/scalar.php` declara o caminho `/scalar`, mas a rota que serve a interface Scalar é `/api-docs`. O caminho `/scalar` não está registrado.
5. As migrations de `permission_role` e `role_user` não declaram chaves estrangeiras.
6. A tabela `addresses` e a tabela `professions` existem, mas não há rota, modelo ou fluxo que as utilize.
7. O nome do cookie removido no logout (`token`) está fixo no código, enquanto o cookie emitido no login usa `JWT_COOKIE_NAME`. Alterar essa variável quebra o logout.
8. A validação do código do tipo de parceiro limita a 3 caracteres, enquanto a coluna no banco é `char(20)`.
9. O `composer.json` declara `"php": "^8.2"`, mas o `composer.lock` fixa dependências que exigem PHP 8.3+ (`maatwebsite/excel`) e 8.4.1+ (cinco pacotes Symfony 8.1). O `vendor/composer/platform_check.php` gerado aborta a aplicação abaixo de 8.4.1.

---

## 12. Critérios de aceite

O comportamento descrito acima é verificado pela suíte de testes do próprio projeto. Para cada entidade com CRUD, o conjunto exigido é:

1. Listagem responde no envelope padrão e pagina corretamente.
2. Listagem funciona com ordenação e com filtro.
3. Listagem, detalhe e edição exigem autenticação.
4. Listagem, detalhe e edição exigem a permissão correspondente; usuário sem permissão recebe `403`.
5. Detalhe com identificador inexistente responde `404`.
6. Cadastro válido responde `201` e persiste o registro.
7. Cadastro com payload inválido responde `422`.
8. Cadastro sem autenticação ou sem permissão é rejeitado.
9. Alteração válida persiste e responde `200`.
10. Alteração com payload inválido ou com identificador inexistente é rejeitada.
11. Exclusão válida responde `204` e o registro fica excluído logicamente.
12. Exclusão com identificador inexistente responde `404`.
13. Exclusão sem autenticação ou sem permissão é rejeitada.
14. Quando a entidade possui busca rápida: estrutura padrão, busca por texto, busca por chaves, paginação e rejeição sem autenticação.
15. Quando a entidade possui exportação: planilha padrão, com filtros, com formato e extensão explícitos, e rejeição de extensão/formato desconhecidos e sem permissão.
16. Quando a entidade possui itens aninhados: criação, alteração, preservação por omissão, remoção por lista vazia, restauração, rollback em item inválido e ausência de consulta em excesso (N+1).

A especificação OpenAPI gerada é validada contra as rotas registradas nos dois sentidos: toda operação documentada tem rota, e toda rota de API documentável está documentada.
