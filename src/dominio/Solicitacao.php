<?php

require_once __DIR__ . '/StatusSolicitacao.php';
require_once __DIR__ . '/Prioridade.php';
require_once __DIR__ . '/Categoria.php';
require_once __DIR__ . '/Solicitante.php';
require_once __DIR__ . '/Atendente.php';

class Solicitacao
{
    private string $id;
    private string $criadaEm;
    private string $titulo;
    private string $descricao;
    private Categoria $categoria;
    private Prioridade $prioridade;
    private StatusSolicitacao $status;
    private Solicitante $solicitante;
    private ?Atendente $atendente = null;

    public function __construct(
        string $titulo,
        string $descricao,
        Categoria $categoria,
        Solicitante $solicitante,
        Prioridade $prioridade = Prioridade::MEDIA
    ) {
        self::validarTexto('Título', $titulo);
        self::validarTexto('Descrição', $descricao);

        $this->id          = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        $this->criadaEm    = date('d/m/Y H:i');
        $this->titulo      = trim($titulo);
        $this->descricao   = trim($descricao);
        $this->categoria   = $categoria;
        $this->solicitante = $solicitante;
        $this->prioridade  = $prioridade;
        $this->status      = StatusSolicitacao::ABERTA;
    }

    public function getId(): string                 { return $this->id; }
    public function getCriadaEm(): string           { return $this->criadaEm; }
    public function getTitulo(): string             { return $this->titulo; }
    public function getDescricao(): string          { return $this->descricao; }
    public function getCategoria(): Categoria       { return $this->categoria; }
    public function getPrioridade(): Prioridade     { return $this->prioridade; }
    public function getStatus(): StatusSolicitacao  { return $this->status; }
    public function getSolicitante(): Solicitante   { return $this->solicitante; }
    public function getAtendente(): ?Atendente      { return $this->atendente; }

    public function iniciarAtendimento(Atendente $atendente): void
    {
        $this->garantirNaoFinalizada();
        if ($this->status !== StatusSolicitacao::ABERTA && $this->status !== StatusSolicitacao::PAUSADA) {
            throw new LogicException("Só é possível iniciar atendimento a partir de ABERTA ou PAUSADA. Status atual: {$this->status->value}");
        }
        $this->atendente = $atendente;
        $this->status    = StatusSolicitacao::EM_ATENDIMENTO;
    }

    public function pausar(): void
    {
        $this->garantirNaoFinalizada();
        if ($this->status !== StatusSolicitacao::EM_ATENDIMENTO) {
            throw new LogicException("Só é possível pausar a partir de EM_ATENDIMENTO. Status atual: {$this->status->value}");
        }
        $this->status = StatusSolicitacao::PAUSADA;
    }

    public function concluir(): void
    {
        $this->garantirNaoFinalizada();
        if ($this->status !== StatusSolicitacao::EM_ATENDIMENTO) {
            throw new LogicException("Só é possível concluir a partir de EM_ATENDIMENTO. Status atual: {$this->status->value}");
        }
        if ($this->atendente === null) {
            throw new LogicException("Não é possível concluir sem atendente associado.");
        }
        $this->status = StatusSolicitacao::CONCLUIDA;
    }

    public function cancelar(): void
    {
        $this->garantirNaoFinalizada();
        $this->status = StatusSolicitacao::CANCELADA;
    }

    private function garantirNaoFinalizada(): void
    {
        if ($this->status->isFinalizado()) {
            throw new LogicException("Operação inválida: solicitação já finalizada ({$this->status->value}).");
        }
    }

    private static function validarTexto(string $campo, string $valor): void
    {
        if (trim($valor) === '') {
            throw new InvalidArgumentException("{$campo} é obrigatório e não pode ser vazio.");
        }
    }

    public function __toString(): string
    {
        $atendente = $this->atendente ? $this->atendente->getNome() : '-';
        return sprintf(
            "[%s] %s | Cat: %s | Prior: %s | Status: %s | Solicitante: %s | Atendente: %s | Criado: %s",
            $this->id,
            $this->titulo,
            $this->categoria->value,
            $this->prioridade->value,
            $this->status->value,
            $this->solicitante->getNome(),
            $atendente,
            $this->criadaEm
        );
    }
}
