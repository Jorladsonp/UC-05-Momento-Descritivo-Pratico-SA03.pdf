# SA3 – Módulo PHP OO com Testes Unitários para Solicitações Internas

**UC:** Programação de Aplicativos
**Foco:** Módulo de domínio OO com testes unitários em PHP
**Turma/Turno:** [TEC.00076]
**Data:** [18/03/2026]

## 1. Objetivo

Entregar um módulo em PHP, orientado a objetos, para registrar e acompanhar solicitações internas, com validações essenciais, controle de transições de status e testes unitários executados via terminal.

## 2. Tecnologias e requisitos

- PHP: 8.1 ou superior (uso de enums, named arguments, arrow functions)
- IDE: VS Code
- Testes: TestRunner próprio em PHP (sem dependências externas)
- Build: sem build (PHP puro, execução via CLI)

## 3. Como executar a aplicação

1. Abrir o terminal na raiz do projeto `SA3_PHP_Solicitacoes/`.
2. Executar o comando:
   ```
   php src/app/main.php
   ```
3. Seguir o menu interativo exibido no console para cadastrar e acompanhar solicitações.

## 4. Como executar os testes unitários

1. Abrir o terminal na raiz do projeto `SA3_PHP_Solicitacoes/`.
2. Executar o comando:
   ```
   php src/test/rodar_testes.php
   ```
3. Resultado esperado: todos os testes passam — `RESULTADO FINAL: TODOS OS TESTES PASSARAM`.

Também é possível executar cada suite individualmente:
```
php src/test/SolicitacaoTest.php
php src/test/RepositorioSolicitacaoTest.php
```

## 5. Requisitos definidos

- **R1:** Cadastrar solicitação com **título** e **descrição** obrigatórios.
- **R2:** Impedir cadastro com título ou descrição vazios/em branco (lançar `InvalidArgumentException` com mensagem clara).
- **R3:** Toda solicitação inicia com **status ABERTA** e **data/hora** de criação gerados automaticamente.
- **R4:** Controlar transições de status:
  - ABERTA → EM_ATENDIMENTO ou CANCELADA
  - EM_ATENDIMENTO → PAUSADA, CONCLUIDA ou CANCELADA
  - PAUSADA → EM_ATENDIMENTO ou CANCELADA
  - CONCLUIDA / CANCELADA: bloqueiam qualquer nova alteração
- **R5:** Ao iniciar atendimento, deve associar um **Atendente** à solicitação.
- **R6:** Permitir listagem/consulta das solicitações no repositório por **status**, **prioridade** e **categoria**.

## 6. Modelagem OO (resumo)

Principais classes:

| Classe | Responsabilidade |
|---|---|
| `Solicitacao` | Entidade do domínio com validações e regras de transição de status |
| `RepositorioSolicitacao` | Armazena em memória e oferece operações de consulta/listagem |
| `Usuario` *(abstrata)* | Base para perfis de usuário (encapsulamento + herança) |
| `Solicitante` | Especializa Usuario com setor e ramal |
| `Atendente` | Especializa Usuario com cargo e especialidade |
| `StatusSolicitacao` *(enum)* | ABERTA, EM_ATENDIMENTO, PAUSADA, CONCLUIDA, CANCELADA |
| `Prioridade` *(enum)* | BAIXA, MEDIA, ALTA, CRITICA |
| `Categoria` *(enum)* | TI, MANUTENCAO, OPERACIONAL, ADMINISTRATIVO |

Onde aparecem técnicas de programação/POO:

- **Encapsulamento:** atributos privados em `Solicitacao`; mudança de status apenas por métodos com regra.
- **Herança:** `Usuario` é abstrata; `Solicitante` e `Atendente` a especializam.
- **Abstração:** método abstrato `getPerfil()` em `Usuario`, implementado em cada subclasse.
- **Tratamento de erros:** `InvalidArgumentException` para dados obrigatórios; `LogicException` para transições inválidas.
- **Testes unitários:** validações e transições cobertas com TestRunner próprio (16 testes).

## 7. Erros identificados e correções (por requisito)

- **Erro 1:** Transição ABERTA → CONCLUIDA era permitida diretamente.
  **Requisito:** R4
  **Correção:** Implementada validação em `concluir()` exigindo status EM_ATENDIMENTO; coberto pelo teste `T5 – naoDeveConcluirSeNaoEstiverEmAtendimento`.

- **Erro 2:** Cadastro aceitava título composto apenas por espaços em branco.
  **Requisito:** R2
  **Correção:** Ajustado `validarTexto()` usando `trim()` antes de verificar string vazia; coberto pelo teste `T2 – naoDeveCriarComTituloEmBranco`.

## 8. Evidências

Arquivos na pasta `evidencias/`:

- No arquivo `prints.docx` possuem prints com as evidências das operações realizadas:

- `execucao_app_01.png` – execução no console mostrando cadastro, mudança de status e listagem.
- `execucao_testes_01.png` – execução da suite de testes (16 testes passando, resultado VERDE).
- `execucao_testes_02.png` – detalhe do teste `T10 – devePercorrerTransicaoCompleta` cobrindo transição completa de status.

## 9. Referências

- Material da UC (AVA): Programação de Aplicativos – PHP e POO
- PHP Manual – Enums: https://www.php.net/manual/pt_BR/language.enumerations.php
- PHP Manual – Exceptions: https://www.php.net/manual/pt_BR/language.exceptions.php
