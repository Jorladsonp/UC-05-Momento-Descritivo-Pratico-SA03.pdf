<?php

abstract class Usuario
{
    private string $id;
    private string $nome;
    private string $email;

    public function __construct(string $id, string $nome, string $email)
    {
        if (trim($id) === '')    throw new InvalidArgumentException("ID do usuário é obrigatório.");
        if (trim($nome) === '')  throw new InvalidArgumentException("Nome do usuário é obrigatório.");
        if (trim($email) === '') throw new InvalidArgumentException("E-mail do usuário é obrigatório.");
        $this->id    = trim($id);
        $this->nome  = trim($nome);
        $this->email = trim($email);
    }

    public function getId(): string    { return $this->id; }
    public function getNome(): string  { return $this->nome; }
    public function getEmail(): string { return $this->email; }

    abstract public function getPerfil(): string;

    public function __toString(): string
    {
        return $this->getPerfil() . "[id={$this->id}, nome={$this->nome}]";
    }
}
