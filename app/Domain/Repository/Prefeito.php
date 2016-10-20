<?php

namespace Domain\Repository;

class Prefeito extends BaseRepository
{
    public function registrarVoto($prefeito)
    {
        $sql = "INSERT INTO votos_prefeitos(prefeito_id) VALUES (?);";

        $result = $this->db->executeUpdate($sql, [$prefeito]);

        if (1 === $result) {
            return true;
        } else {
            return false;
        }
    }
}
