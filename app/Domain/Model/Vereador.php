<?php

namespace Domain\Model;

class Vereador implements \JsonSerializable
{
    private $id;
    private $nome;
    private $partido;
    private $foto;
    private $votos = 0;

    public function __construct($id, $nome, $partido, $foto)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->partido = $partido;
        $this->foto = $foto;
    }

    public function setVotos($votos)
    {
        $this->votos = $votos;
    }

    public function jsonSerialize()
    {
        return [
            'numero' => $this->id,
            'nome' => $this->nome,
            'partido' => $this->partido,
            'foto' => $this->foto,
            'votos' => $this->votos,
        ];
    }
}
