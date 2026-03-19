<?php

require_once __DIR__ . '/TestRunner.php';
require_once __DIR__ . '/../dominio/StatusSolicitacao.php';
require_once __DIR__ . '/../dominio/Prioridade.php';
require_once __DIR__ . '/../dominio/Categoria.php';
require_once __DIR__ . '/../dominio/Usuario.php';
require_once __DIR__ . '/../dominio/Solicitante.php';
require_once __DIR__ . '/../dominio/Atendente.php';
require_once __DIR__ . '/../dominio/Solicitacao.php';
require_once __DIR__ . '/../dominio/RepositorioSolicitacao.php';

$t = new TestRunner();

$solicitante = new Solicitante('S01', 'Carlos Neto',   'carlos@empresa.com',  'Produção',  '2200');
$atendente   = new Atendente('A01', 'Diana Matos',    'diana@empresa.com',   'Analista',  Categoria::OPERACIONAL);

// ---------------------------------------------------------------
//  T1 – R6: Repositório inicia vazio
// ---------------------------------------------------------------
$t->executar('T1 – repositorioIniciaVazio', function () use ($t) {
    $repo = new RepositorioSolicitacao();
    $t->assertIgual(0, $repo->total(), 'Repositório deve iniciar com 0 itens.');
    $t->assertIgual([], $repo->listarTodas(), 'Lista deve estar vazia.');
});

// ---------------------------------------------------------------
//  T2 – R1: Deve adicionar e recuperar solicitação pelo ID
// ---------------------------------------------------------------
$t->executar('T2 – deveAdicionarEBuscarPorId', function () use ($t, $solicitante) {
    $repo = new RepositorioSolicitacao();
    $s    = new Solicitacao('Erro no sistema', 'Sistema retorna erro 500.', Categoria::TI, $solicitante);
    $repo->adicionar($s);
    $t->assertIgual(1, $repo->total());
    $encontrada = $repo->buscarPorId($s->getId());
    $t->assertVerdadeiro($encontrada !== null, 'Deve encontrar solicitação pelo ID.');
    $t->assertIgual($s->getId(), $encontrada->getId());
});

// ---------------------------------------------------------------
//  T3 – Busca por ID inexistente deve retornar null
// ---------------------------------------------------------------
$t->executar('T3 – buscarIdInexistenteRetornaNull', function () use ($t) {
    $repo = new RepositorioSolicitacao();
    $t->assertIgual(null, $repo->buscarPorId('XXXXXXXX'), 'ID inexistente deve retornar null.');
});

// ---------------------------------------------------------------
//  T4 – R6: Deve listar solicitações por status
// ---------------------------------------------------------------
$t->executar('T4 – deveListarPorStatus', function () use ($t, $solicitante, $atendente) {
    $repo = new RepositorioSolicitacao();
    $s1   = new Solicitacao('Problema A', 'Desc A.', Categoria::TI, $solicitante);
    $s2   = new Solicitacao('Problema B', 'Desc B.', Categoria::TI, $solicitante);
    $s3   = new Solicitacao('Problema C', 'Desc C.', Categoria::TI, $solicitante);
    $repo->adicionar($s1);
    $repo->adicionar($s2);
    $repo->adicionar($s3);

    $s1->iniciarAtendimento($atendente);
    $s2->cancelar();

    $abertas      = $repo->listarPorStatus(StatusSolicitacao::ABERTA);
    $emAtendimento = $repo->listarPorStatus(StatusSolicitacao::EM_ATENDIMENTO);
    $canceladas   = $repo->listarPorStatus(StatusSolicitacao::CANCELADA);

    $t->assertIgual(1, count($abertas),       'Deve haver 1 solicitação ABERTA.');
    $t->assertIgual(1, count($emAtendimento), 'Deve haver 1 solicitação EM_ATENDIMENTO.');
    $t->assertIgual(1, count($canceladas),    'Deve haver 1 solicitação CANCELADA.');
});

// ---------------------------------------------------------------
//  T5 – R6: Deve listar solicitações por prioridade
// ---------------------------------------------------------------
$t->executar('T5 – deveListarPorPrioridade', function () use ($t, $solicitante) {
    $repo = new RepositorioSolicitacao();
    $s1   = new Solicitacao('Alta prior',  'Desc.', Categoria::TI, $solicitante, Prioridade::ALTA);
    $s2   = new Solicitacao('Media prior', 'Desc.', Categoria::TI, $solicitante, Prioridade::MEDIA);
    $s3   = new Solicitacao('Alta prior 2','Desc.', Categoria::TI, $solicitante, Prioridade::ALTA);
    $repo->adicionar($s1);
    $repo->adicionar($s2);
    $repo->adicionar($s3);

    $altas  = $repo->listarPorPrioridade(Prioridade::ALTA);
    $medias = $repo->listarPorPrioridade(Prioridade::MEDIA);

    $t->assertIgual(2, count($altas),  'Deve haver 2 solicitações de prioridade ALTA.');
    $t->assertIgual(1, count($medias), 'Deve haver 1 solicitação de prioridade MÉDIA.');
});

// ---------------------------------------------------------------
//  T6 – R6: Deve listar solicitações por categoria
// ---------------------------------------------------------------
$t->executar('T6 – deveListarPorCategoria', function () use ($t, $solicitante) {
    $repo = new RepositorioSolicitacao();
    $s1   = new Solicitacao('TI 1',    'Desc.', Categoria::TI,         $solicitante);
    $s2   = new Solicitacao('Manu 1',  'Desc.', Categoria::MANUTENCAO, $solicitante);
    $s3   = new Solicitacao('TI 2',    'Desc.', Categoria::TI,         $solicitante);
    $repo->adicionar($s1);
    $repo->adicionar($s2);
    $repo->adicionar($s3);

    $ti   = $repo->listarPorCategoria(Categoria::TI);
    $manu = $repo->listarPorCategoria(Categoria::MANUTENCAO);

    $t->assertIgual(2, count($ti),   'Deve haver 2 solicitações de TI.');
    $t->assertIgual(1, count($manu), 'Deve haver 1 solicitação de MANUTENÇÃO.');
});

echo str_repeat('=', 65) . PHP_EOL;
echo "  TESTES – RepositorioSolicitacaoTest" . PHP_EOL;
echo str_repeat('=', 65) . PHP_EOL;
$t->resumo();
return $t->getFalhou();
