<?php

namespace Domain\Model;

class Eleitor implements \JsonSerializable
{
    private $titulo;

    private $senha;

    public function __construct($titulo, $senha)
    {
        $this->titulo = $titulo;
        $this->senha = $senha;
    }

    public function criptografarSenha($senha)
    {
        $this->senha = sha1($senha);
    }

    public function senhaValida($senha)
    {
        return $this->senha === sha1($senha);
    }

    public function getSenhaCriptografada()
    {
        return $this->senha;
    }

    public function jsonSerialize()
    {
        return ['titulo' => $this->titulo];
    }
}
