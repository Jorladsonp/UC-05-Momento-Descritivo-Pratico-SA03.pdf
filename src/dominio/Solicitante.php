<?php

require_once __DIR__ . '/Usuario.php';

class Solicitante extends Usuario
{
    private string $setor;
    private string $ramal;

    public function __construct(string $id, string $nome, string $email, string $setor, string $ramal)
    {
        parent::__construct($id, $nome, $email);
        if (trim($setor) === '') throw new InvalidArgumentException("Setor do solicitante é obrigatório.");
        $this->setor = trim($setor);
        $this->ramal = trim($ramal);
    }

    public function getSetor(): string { return $this->setor; }
    public function getRamal(): string { return $this->ramal; }

    public function getPerfil(): string { return 'Solicitante'; }
}
