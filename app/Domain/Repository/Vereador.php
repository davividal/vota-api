<?php

namespace Domain\Repository;

use Domain\Model\Vereador as VereadorModel;

class Vereador extends BaseRepository
{
    public function findAll()
    {
        $url = 'https://dl.dropboxusercontent.com/u/40990541/vereador.json';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = json_decode(curl_exec($ch))['vereador'];

        $vereadores = [];
        foreach ($data as $vereador) {
            $id = $vereador->id;
            $nome = $vereador->nome;
            $partido = $vereador->partido;
            $foto = $vereador->foto;
            $vereadores[$id] = new VereadorModel($id, $nome, $partido, $foto);
        }

        return $vereadores;
    }

    public function resultadoVereadores()
    {
        $sql = 'SELECT vereador_id, COUNT(*) AS votos FROM votos_vereadores GROUP BY vereador_id';
        $data = $this->db->fetchAll($sql);

        $vereadores = $this->findAll();

        $votos = [];
        foreach ($data as $rawVoto) {
            $id = $rawVoto['vereador_id'];
            $votos = $rawVoto['votos'];

            $vereadores[$id]->setVotos($votos);

            $votos[] = $vereadores[$id];
        }

        return $votos;
    }

    public function registrarVoto($vereador)
    {
        $sql = 'INSERT INTO votos_vereadores(vereador_id) VALUES (?);';

        $result = $this->db->executeUpdate($sql, [$vereador]);

        if (1 === $result) {
            return true;
        } else {
            return false;
        }
    }
}
