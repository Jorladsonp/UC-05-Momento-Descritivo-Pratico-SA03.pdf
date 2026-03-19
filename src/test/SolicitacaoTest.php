<?php

require_once __DIR__ . '/TestRunner.php';
require_once __DIR__ . '/../dominio/StatusSolicitacao.php';
require_once __DIR__ . '/../dominio/Prioridade.php';
require_once __DIR__ . '/../dominio/Categoria.php';
require_once __DIR__ . '/../dominio/Usuario.php';
require_once __DIR__ . '/../dominio/Solicitante.php';
require_once __DIR__ . '/../dominio/Atendente.php';
require_once __DIR__ . '/../dominio/Solicitacao.php';

$t = new TestRunner();

// Dados auxiliares reutilizados nos testes
$solicitante = new Solicitante('S01', 'Ana Paula', 'ana@empresa.com', 'TI', '1100');
$atendente   = new Atendente('A01', 'Bruno Silva', 'bruno@empresa.com', 'Técnico', Categoria::TI);

// ---------------------------------------------------------------
//  T1 – R2: Não deve criar solicitação com título vazio
// ---------------------------------------------------------------
$t->executar('T1 – naoDeveCriarComTituloVazio', function () use ($t, $solicitante) {
    $t->assertLancaExcecao(
        fn() => new Solicitacao('', 'Descrição válida', Categoria::TI, $solicitante),
        InvalidArgumentException::class,
        'Título'
    );
});

// ---------------------------------------------------------------
//  T2 – R2: Não deve criar solicitação com título só de espaços
// ---------------------------------------------------------------
$t->executar('T2 – naoDeveCriarComTituloEmBranco', function () use ($t, $solicitante) {
    $t->assertLancaExcecao(
        fn() => new Solicitacao('   ', 'Descrição válida', Categoria::TI, $solicitante),
        InvalidArgumentException::class,
        'Título'
    );
});

// ---------------------------------------------------------------
//  T3 – R2: Não deve criar solicitação com descrição vazia
// ---------------------------------------------------------------
$t->executar('T3 – naoDeveCriarComDescricaoVazia', function () use ($t, $solicitante) {
    $t->assertLancaExcecao(
        fn() => new Solicitacao('Título válido', '', Categoria::TI, $solicitante),
        InvalidArgumentException::class,
        'Descrição'
    );
});

// ---------------------------------------------------------------
//  T4 – R3: Deve iniciar com status ABERTA e data de criação
// ---------------------------------------------------------------
$t->executar('T4 – deveIniciarComoAbertaComDataCriacao', function () use ($t, $solicitante) {
    $s = new Solicitacao('Computador travando', 'Liga e trava na inicialização.', Categoria::TI, $solicitante);
    $t->assertIgual(StatusSolicitacao::ABERTA, $s->getStatus(), 'Status inicial deve ser ABERTA.');
    $t->assertVerdadeiro($s->getCriadaEm() !== '', 'Data de criação deve ser preenchida.');
    $t->assertVerdadeiro($s->getId() !== '', 'ID deve ser gerado automaticamente.');
});

// ---------------------------------------------------------------
//  T5 – R4: Não deve concluir se não estiver EM_ATENDIMENTO
// ---------------------------------------------------------------
$t->executar('T5 – naoDeveConcluirSeNaoEstiverEmAtendimento', function () use ($t, $solicitante) {
    $s = new Solicitacao('Impressora offline', 'Impressora parou de funcionar.', Categoria::TI, $solicitante);
    $t->assertLancaExcecao(
        fn() => $s->concluir(),
        LogicException::class,
        'EM_ATENDIMENTO'
    );
});

// ---------------------------------------------------------------
//  T6 – R4: Não deve pausar se não estiver EM_ATENDIMENTO
// ---------------------------------------------------------------
$t->executar('T6 – naoDevePausarSeNaoEstiverEmAtendimento', function () use ($t, $solicitante) {
    $s = new Solicitacao('Falha de rede', 'Sem acesso à internet.', Categoria::TI, $solicitante);
    $t->assertLancaExcecao(
        fn() => $s->pausar(),
        LogicException::class,
        'EM_ATENDIMENTO'
    );
});

// ---------------------------------------------------------------
//  T7 – R4: Não deve alterar solicitação já concluída
// ---------------------------------------------------------------
$t->executar('T7 – naoDeveAlterarSolicitacaoJaConcluida', function () use ($t, $solicitante, $atendente) {
    $s = new Solicitacao('Servidor fora', 'Servidor web caiu.', Categoria::TI, $solicitante);
    $s->iniciarAtendimento($atendente);
    $s->concluir();
    $t->assertLancaExcecao(
        fn() => $s->cancelar(),
        LogicException::class,
        'finalizada'
    );
});

// ---------------------------------------------------------------
//  T8 – R4: Não deve alterar solicitação já cancelada
// ---------------------------------------------------------------
$t->executar('T8 – naoDeveAlterarSolicitacaoJaCancelada', function () use ($t, $solicitante, $atendente) {
    $s = new Solicitacao('Cabo danificado', 'Cabo de rede partido.', Categoria::MANUTENCAO, $solicitante);
    $s->cancelar();
    $t->assertLancaExcecao(
        fn() => $s->iniciarAtendimento($atendente),
        LogicException::class,
        'finalizada'
    );
});

// ---------------------------------------------------------------
//  T9 – R5: Deve associar atendente ao iniciar atendimento
// ---------------------------------------------------------------
$t->executar('T9 – deveAssociarAtendenteAoIniciarAtendimento', function () use ($t, $solicitante, $atendente) {
    $s = new Solicitacao('Antivírus desatualizado', 'Software de proteção desatualizado.', Categoria::TI, $solicitante);
    $t->assertIgual(null, $s->getAtendente(), 'Atendente deve ser null antes do atendimento.');
    $s->iniciarAtendimento($atendente);
    $t->assertIgual(StatusSolicitacao::EM_ATENDIMENTO, $s->getStatus());
    $t->assertIgual($atendente, $s->getAtendente(), 'Atendente deve estar associado após iniciarAtendimento.');
});

// ---------------------------------------------------------------
//  T10 – R4: Transição completa ABERTA → EM_ATENDIMENTO → PAUSADA → EM_ATENDIMENTO → CONCLUIDA
// ---------------------------------------------------------------
$t->executar('T10 – devePercorrerTransicaoCompleta', function () use ($t, $solicitante, $atendente) {
    $s = new Solicitacao('Backup falhou', 'Rotina de backup não executou.', Categoria::TI, $solicitante);
    $t->assertIgual(StatusSolicitacao::ABERTA, $s->getStatus());
    $s->iniciarAtendimento($atendente);
    $t->assertIgual(StatusSolicitacao::EM_ATENDIMENTO, $s->getStatus());
    $s->pausar();
    $t->assertIgual(StatusSolicitacao::PAUSADA, $s->getStatus());
    $s->iniciarAtendimento($atendente);
    $t->assertIgual(StatusSolicitacao::EM_ATENDIMENTO, $s->getStatus());
    $s->concluir();
    $t->assertIgual(StatusSolicitacao::CONCLUIDA, $s->getStatus());
});

echo str_repeat('=', 65) . PHP_EOL;
echo "  TESTES – SolicitacaoTest" . PHP_EOL;
echo str_repeat('=', 65) . PHP_EOL;
$t->resumo();
return $t->getFalhou();
