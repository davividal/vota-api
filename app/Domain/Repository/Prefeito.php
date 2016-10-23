<?php

namespace Domain\Repository;

use Domain\Model\Prefeito as PrefeitoModel;

class Prefeito extends BaseRepository
{
    public function findAll()
    {
        $url = 'https://dl.dropboxusercontent.com/u/40990541/prefeito.json';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = json_decode(curl_exec($ch));

        $prefeitos = [];
        foreach ($data->prefeito as $prefeito) {
            $id = $prefeito->id;
            $nome = $prefeito->nome;
            $partido = $prefeito->partido;
            $foto = $prefeito->foto;

            $prefeitos[$id] = new PrefeitoModel($id, $nome, $partido, $foto);
        }

        return $prefeitos;
    }

    public function resultadoPrefeitos()
    {
        $sql = 'SELECT prefeito_id, COUNT(*) AS votos FROM votos_prefeitos GROUP BY prefeito_id';
        $data = $this->db->fetchAll($sql);

        $prefeitos = $this->findAll();

        foreach ($data as $rawVoto) {
            $id = $rawVoto['prefeito_id'];
            $votos = $rawVoto['votos'];

            $prefeitos[$id]->setVotos($votos);
        }

        return $prefeitos;
    }

    public function registrarVoto($prefeito)
    {
        $sql = 'INSERT INTO votos_prefeitos(prefeito_id) VALUES (?);';

        $result = $this->db->executeUpdate($sql, [$prefeito]);

        if (1 === $result) {
            return true;
        } else {
            return false;
        }
    }
}
