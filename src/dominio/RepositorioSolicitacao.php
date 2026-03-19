<?php

require_once __DIR__ . '/Solicitacao.php';
require_once __DIR__ . '/StatusSolicitacao.php';
require_once __DIR__ . '/Prioridade.php';
require_once __DIR__ . '/Categoria.php';

class RepositorioSolicitacao
{
    private array $itens = [];

    public function adicionar(Solicitacao $s): void
    {
        $this->itens[] = $s;
    }

    public function buscarPorId(string $id): ?Solicitacao
    {
        foreach ($this->itens as $s) {
            if (strtoupper(trim($id)) === $s->getId()) return $s;
        }
        return null;
    }

    public function listarTodas(): array
    {
        return $this->itens;
    }

    public function listarPorStatus(StatusSolicitacao $status): array
    {
        return array_values(array_filter($this->itens, fn($s) => $s->getStatus() === $status));
    }

    public function listarPorPrioridade(Prioridade $prioridade): array
    {
        return array_values(array_filter($this->itens, fn($s) => $s->getPrioridade() === $prioridade));
    }

    public function listarPorCategoria(Categoria $categoria): array
    {
        return array_values(array_filter($this->itens, fn($s) => $s->getCategoria() === $categoria));
    }

    public function total(): int
    {
        return count($this->itens);
    }
}
