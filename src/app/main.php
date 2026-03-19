<?php

require_once __DIR__ . '/../dominio/StatusSolicitacao.php';
require_once __DIR__ . '/../dominio/Prioridade.php';
require_once __DIR__ . '/../dominio/Categoria.php';
require_once __DIR__ . '/../dominio/Usuario.php';
require_once __DIR__ . '/../dominio/Solicitante.php';
require_once __DIR__ . '/../dominio/Atendente.php';
require_once __DIR__ . '/../dominio/Solicitacao.php';
require_once __DIR__ . '/../dominio/RepositorioSolicitacao.php';

function linha(): void { echo str_repeat('-', 65) . PHP_EOL; }

function cabecalho(string $titulo): void
{
    linha();
    echo "  {$titulo}" . PHP_EOL;
    linha();
}

function lerEntrada(string $prompt): string
{
    echo $prompt;
    return trim(fgets(STDIN));
}

function exibirLista(array $itens, string $vazio = 'Nenhuma solicitação encontrada.'): void
{
    if (empty($itens)) {
        echo "  {$vazio}" . PHP_EOL;
        return;
    }
    foreach ($itens as $item) {
        echo "  {$item}" . PHP_EOL;
    }
}

// ---------------------------------------------------------------
//  Dados iniciais para demonstração
// ---------------------------------------------------------------
$solicitante1 = new Solicitante('S01', 'Marina Alves',  'marina@fabrica.com',  'Produção',      '1210');
$solicitante2 = new Solicitante('S02', 'Roberto Lima',  'roberto@fabrica.com', 'Almoxarifado',  '1340');

$atendente1   = new Atendente('A01', 'Carlos Duarte',   'carlos@fabrica.com',   'Técnico',    Categoria::TI);
$atendente2   = new Atendente('A02', 'Fernanda Costa',  'fernanda@fabrica.com', 'Engenheira', Categoria::MANUTENCAO);

$repo = new RepositorioSolicitacao();

// ---------------------------------------------------------------
//  Menu principal
// ---------------------------------------------------------------
while (true) {
    cabecalho('SISTEMA DE SOLICITAÇÕES INTERNAS – SA3');
    echo "  1. Nova solicitação" . PHP_EOL;
    echo "  2. Iniciar atendimento" . PHP_EOL;
    echo "  3. Pausar atendimento" . PHP_EOL;
    echo "  4. Retomar atendimento" . PHP_EOL;
    echo "  5. Concluir solicitação" . PHP_EOL;
    echo "  6. Cancelar solicitação" . PHP_EOL;
    echo "  7. Listar todas" . PHP_EOL;
    echo "  8. Listar por status" . PHP_EOL;
    echo "  9. Listar por prioridade" . PHP_EOL;
    echo " 10. Listar por categoria" . PHP_EOL;
    echo "  0. Sair" . PHP_EOL;
    linha();

    $opcao = lerEntrada("  Opção: ");

    switch ($opcao) {

        // ---- NOVA SOLICITAÇÃO -----------------------------------
        case '1':
            cabecalho('NOVA SOLICITAÇÃO');
            echo "  Solicitantes disponíveis:" . PHP_EOL;
            echo "    [1] {$solicitante1->getNome()} – {$solicitante1->getSetor()}" . PHP_EOL;
            echo "    [2] {$solicitante2->getNome()} – {$solicitante2->getSetor()}" . PHP_EOL;
            $esc        = lerEntrada("  Escolha o solicitante: ");
            $solicitante = $esc === '2' ? $solicitante2 : $solicitante1;

            echo PHP_EOL . "  Categorias:" . PHP_EOL;
            $cats = Categoria::cases();
            foreach ($cats as $i => $cat) {
                echo "    [" . ($i + 1) . "] {$cat->value}" . PHP_EOL;
            }
            $escCat   = (int) lerEntrada("  Escolha a categoria: ");
            $categoria = $cats[max(0, $escCat - 1)];

            echo PHP_EOL . "  Prioridades:" . PHP_EOL;
            $prios = Prioridade::cases();
            foreach ($prios as $i => $p) {
                echo "    [" . ($i + 1) . "] {$p->value}" . PHP_EOL;
            }
            $escPrio  = (int) lerEntrada("  Escolha a prioridade: ");
            $prioridade = $prios[max(0, $escPrio - 1)];

            $titulo    = lerEntrada("  Título: ");
            $descricao = lerEntrada("  Descrição: ");

            try {
                $s = new Solicitacao($titulo, $descricao, $categoria, $solicitante, $prioridade);
                $repo->adicionar($s);
                echo PHP_EOL . "  Solicitação criada com sucesso:" . PHP_EOL;
                echo "  {$s}" . PHP_EOL;
            } catch (InvalidArgumentException $e) {
                echo PHP_EOL . "  ERRO DE VALIDAÇÃO: " . $e->getMessage() . PHP_EOL;
            }
            break;

        // ---- INICIAR ATENDIMENTO --------------------------------
        case '2':
            cabecalho('INICIAR ATENDIMENTO');
            $id = strtoupper(lerEntrada("  ID da solicitação: "));
            $s  = $repo->buscarPorId($id);
            if ($s === null) { echo "  Solicitação não encontrada." . PHP_EOL; break; }

            echo "  Atendentes disponíveis:" . PHP_EOL;
            echo "    [1] {$atendente1->getNome()} – {$atendente1->getCargo()} ({$atendente1->getEspecialidade()->value})" . PHP_EOL;
            echo "    [2] {$atendente2->getNome()} – {$atendente2->getCargo()} ({$atendente2->getEspecialidade()->value})" . PHP_EOL;
            $esc      = lerEntrada("  Escolha o atendente: ");
            $atendente = $esc === '2' ? $atendente2 : $atendente1;

            try {
                $s->iniciarAtendimento($atendente);
                echo "  Atendimento iniciado: {$s}" . PHP_EOL;
            } catch (LogicException $e) {
                echo "  ERRO: " . $e->getMessage() . PHP_EOL;
            }
            break;

        // ---- PAUSAR ---------------------------------------------
        case '3':
            cabecalho('PAUSAR ATENDIMENTO');
            $id = strtoupper(lerEntrada("  ID da solicitação: "));
            $s  = $repo->buscarPorId($id);
            if ($s === null) { echo "  Solicitação não encontrada." . PHP_EOL; break; }
            try {
                $s->pausar();
                echo "  Pausada: {$s}" . PHP_EOL;
            } catch (LogicException $e) {
                echo "  ERRO: " . $e->getMessage() . PHP_EOL;
            }
            break;

        // ---- RETOMAR --------------------------------------------
        case '4':
            cabecalho('RETOMAR ATENDIMENTO');
            $id = strtoupper(lerEntrada("  ID da solicitação: "));
            $s  = $repo->buscarPorId($id);
            if ($s === null) { echo "  Solicitação não encontrada." . PHP_EOL; break; }

            echo "  Atendentes disponíveis:" . PHP_EOL;
            echo "    [1] {$atendente1->getNome()}" . PHP_EOL;
            echo "    [2] {$atendente2->getNome()}" . PHP_EOL;
            $esc      = lerEntrada("  Escolha o atendente: ");
            $atendente = $esc === '2' ? $atendente2 : $atendente1;

            try {
                $s->iniciarAtendimento($atendente);
                echo "  Retomada: {$s}" . PHP_EOL;
            } catch (LogicException $e) {
                echo "  ERRO: " . $e->getMessage() . PHP_EOL;
            }
            break;

        // ---- CONCLUIR ------------------------------------------
        case '5':
            cabecalho('CONCLUIR SOLICITAÇÃO');
            $id = strtoupper(lerEntrada("  ID da solicitação: "));
            $s  = $repo->buscarPorId($id);
            if ($s === null) { echo "  Solicitação não encontrada." . PHP_EOL; break; }
            try {
                $s->concluir();
                echo "  Concluída: {$s}" . PHP_EOL;
            } catch (LogicException $e) {
                echo "  ERRO: " . $e->getMessage() . PHP_EOL;
            }
            break;

        // ---- CANCELAR ------------------------------------------
        case '6':
            cabecalho('CANCELAR SOLICITAÇÃO');
            $id = strtoupper(lerEntrada("  ID da solicitação: "));
            $s  = $repo->buscarPorId($id);
            if ($s === null) { echo "  Solicitação não encontrada." . PHP_EOL; break; }
            try {
                $s->cancelar();
                echo "  Cancelada: {$s}" . PHP_EOL;
            } catch (LogicException $e) {
                echo "  ERRO: " . $e->getMessage() . PHP_EOL;
            }
            break;

        // ---- LISTAR TODAS --------------------------------------
        case '7':
            cabecalho("TODAS AS SOLICITAÇÕES ({$repo->total()} registro(s))");
            exibirLista($repo->listarTodas());
            break;

        // ---- LISTAR POR STATUS ---------------------------------
        case '8':
            cabecalho('LISTAR POR STATUS');
            $statuses = StatusSolicitacao::cases();
            foreach ($statuses as $i => $st) {
                echo "  [" . ($i + 1) . "] {$st->value}" . PHP_EOL;
            }
            $esc    = (int) lerEntrada("  Escolha o status: ");
            $status = $statuses[max(0, $esc - 1)];
            echo PHP_EOL . "  Status: {$status->value}" . PHP_EOL;
            exibirLista($repo->listarPorStatus($status));
            break;

        // ---- LISTAR POR PRIORIDADE -----------------------------
        case '9':
            cabecalho('LISTAR POR PRIORIDADE');
            $prioridades = Prioridade::cases();
            foreach ($prioridades as $i => $pr) {
                echo "  [" . ($i + 1) . "] {$pr->value}" . PHP_EOL;
            }
            $esc       = (int) lerEntrada("  Escolha a prioridade: ");
            $prioridade = $prioridades[max(0, $esc - 1)];
            echo PHP_EOL . "  Prioridade: {$prioridade->value}" . PHP_EOL;
            exibirLista($repo->listarPorPrioridade($prioridade));
            break;

        // ---- LISTAR POR CATEGORIA ------------------------------
        case '10':
            cabecalho('LISTAR POR CATEGORIA');
            $cats = Categoria::cases();
            foreach ($cats as $i => $cat) {
                echo "  [" . ($i + 1) . "] {$cat->value}" . PHP_EOL;
            }
            $esc      = (int) lerEntrada("  Escolha a categoria: ");
            $categoria = $cats[max(0, $esc - 1)];
            echo PHP_EOL . "  Categoria: {$categoria->value}" . PHP_EOL;
            exibirLista($repo->listarPorCategoria($categoria));
            break;

        // ---- SAIR ----------------------------------------------
        case '0':
            echo PHP_EOL . "  Encerrando o sistema. Até logo!" . PHP_EOL;
            exit(0);

        default:
            echo "  Opção inválida." . PHP_EOL;
    }

    echo PHP_EOL;
    lerEntrada("  Pressione ENTER para continuar...");
    echo PHP_EOL;
}
