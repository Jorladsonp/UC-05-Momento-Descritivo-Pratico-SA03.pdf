<?php

require_once __DIR__ . '/Usuario.php';
require_once __DIR__ . '/Categoria.php';

class Atendente extends Usuario
{
    private string $cargo;
    private Categoria $especialidade;

    public function __construct(string $id, string $nome, string $email, string $cargo, Categoria $especialidade)
    {
        parent::__construct($id, $nome, $email);
        if (trim($cargo) === '') throw new InvalidArgumentException("Cargo do atendente é obrigatório.");
        $this->cargo         = trim($cargo);
        $this->especialidade = $especialidade;
    }

    public function getCargo(): string             { return $this->cargo; }
    public function getEspecialidade(): Categoria  { return $this->especialidade; }

    public function getPerfil(): string { return 'Atendente'; }
}
