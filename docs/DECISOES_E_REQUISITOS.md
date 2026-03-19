# SA3 – Requisitos e Decisões

## 1) Requisitos do módulo (definidos pela dupla)

- R1: Criar solicitação com título e descrição obrigatórios.
- R2: Bloquear criação com campos em branco ou vazios (mensagem de erro clara).
- R3: Toda solicitação inicia com status ABERTA e registra data/hora de criação automaticamente.
- R4: Controlar transições válidas de status (ABERTA → EM_ATENDIMENTO/CANCELADA; EM_ATENDIMENTO → PAUSADA/CONCLUIDA/CANCELADA; PAUSADA → EM_ATENDIMENTO/CANCELADA; estados finais bloqueiam alterações).
- R5: Associar atendente obrigatoriamente ao iniciar atendimento.
- R6: Listar solicitações por status, prioridade e categoria no repositório.

## 2) Regras do domínio implementadas

- Dados obrigatórios:
  - `titulo` e `descricao` não podem ser nulos, vazios ou compostos só de espaços (verificado com `trim()`).
  - `solicitante` deve existir ao criar a solicitação.
- Status/andamento:
  - Estado inicial: ABERTA.
  - Transições controladas conforme R4 via métodos `iniciarAtendimento()`, `pausar()`, `concluir()`, `cancelar()`.
  - Estados finais: CONCLUIDA ou CANCELADA — bloqueiam qualquer operação via `garantirNaoFinalizada()`.
- Validações principais:
  - `concluir()` só é permitido a partir de EM_ATENDIMENTO e exige atendente associado.
  - `pausar()` só é permitido a partir de EM_ATENDIMENTO.
  - `iniciarAtendimento(atendente)` exige status ABERTA ou PAUSADA.

## 3) Estratégia de testes unitários

### Classes/funcionalidades testadas

- `Solicitacao`: validações de criação (R1/R2), status inicial (R3), transições (R4), associação de atendente (R5).
- `RepositorioSolicitacao`: adicionar, buscar por ID, listar por status/prioridade/categoria (R6).

### Casos de teste

- T1: `naoDeveCriarComTituloVazio` (R2)
- T2: `naoDeveCriarComTituloEmBranco` (R2)
- T3: `naoDeveCriarComDescricaoVazia` (R2)
- T4: `deveIniciarComoAbertaComDataCriacao` (R3)
- T5: `naoDeveConcluirSeNaoEstiverEmAtendimento` (R4)
- T6: `naoDevePausarSeNaoEstiverEmAtendimento` (R4)
- T7: `naoDeveAlterarSolicitacaoJaConcluida` (R4)
- T8: `naoDeveAlterarSolicitacaoJaCancelada` (R4)
- T9: `deveAssociarAtendenteAoIniciarAtendimento` (R5)
- T10: `devePercorrerTransicaoCompleta` (R4)
- T11: `repositorioIniciaVazio` (R6)
- T12: `deveAdicionarEBuscarPorId` (R1/R6)
- T13: `buscarIdInexistenteRetornaNull` (R6)
- T14: `deveListarPorStatus` (R6)
- T15: `deveListarPorPrioridade` (R6)
- T16: `deveListarPorCategoria` (R6)

## 4) Rastreabilidade (como registramos a evolução)

- Forma utilizada: commits no repositório Git + registro resumido no README (seção 7).

Breve histórico:

- 19/03/2026 – Criação da estrutura de pacotes e enums do domínio (StatusSolicitacao, Prioridade, Categoria).
- 19/03/2026 – Implementação das classes de usuário (Usuario abstrata, Solicitante, Atendente) e entidade Solicitacao com validações (R1/R2/R3).
- 19/03/2026 – Implementação das regras de transição de status em Solicitacao (R4/R5) e RepositorioSolicitacao (R6).
- 19/03/2026 – Criação do TestRunner próprio e suites SolicitacaoTest (10 testes) e RepositorioSolicitacaoTest (6 testes); correção dos erros identificados (Erro 1 e Erro 2).
- 19/03/2026 – Finalização do menu interativo (main.php) e documentação (README + DECISOES).
