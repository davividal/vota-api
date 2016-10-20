<?php

namespace Domain\Repository;

class Vereador extends BaseRepository
{
    public function registrarVoto($vereador)
    {
        $sql = "INSERT INTO votos_vereadores(vereador_id) VALUES (?);";

        $result = $this->db->executeUpdate($sql, [$vereador]);

        if (1 === $result) {
            return true;
        } else {
            return false;
        }
    }
}
