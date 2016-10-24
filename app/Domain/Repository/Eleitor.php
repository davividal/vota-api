<?php

namespace Domain\Repository;

use Domain\Model\Eleitor as EleitorModel;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class Eleitor extends BaseRepository
{
    public function findAll()
    {
        $sql = 'SELECT titulo, senha FROM eleitores';
        $data = $this->db->fetchAll($sql);

        $eleitores = [];
        foreach ($data as $rawEleitor) {
            $eleitores[] = new EleitorModel(
                $rawEleitor['titulo'],
                $rawEleitor['senha']
            );
        }

        return $eleitores;
    }

    public function find($titulo)
    {
        $sql = 'SELECT titulo, senha FROM eleitores WHERE titulo = ?';
        $data = $this->db->fetchAll($sql, [$titulo]);

        if (1 !== count($data)) {
            return new EleitorModel(null, null);
        }

        return new EleitorModel(
            $data[0]['titulo'],
            trim($data[0]['senha'])
        );
    }

    public function register($titulo, $senhaRaw)
    {
        $eleitor = new EleitorModel($titulo, $senhaRaw);
        $eleitor->criptografarSenha($senhaRaw);
        $senha = $eleitor->getSenhaCriptografada();

        $this->db->beginTransaction();

        $sql = 'INSERT INTO eleitores(titulo, senha) VALUES (?, ?)';

        try {
            $result = $this->db->executeUpdate($sql, [$titulo, $senha]);
        } catch (UniqueConstraintViolationException $e) {
            // TODO: log
            $this->db->rollBack();

            return false;
        }

        if (1 === $result) {
            $this->db->commit();

            return true;
        } else {
            $this->db->rollBack();

            return false;
        }
    }

    public function registrarVoto($titulo)
    {
        $sql = 'UPDATE eleitores SET votou = true WHERE titulo = ? AND votou = false';

        $result = $this->db->executeUpdate($sql, [$titulo]);

        return $result;
    }

    public function novaEleicao()
    {
        $sql = 'UPDATE eleitores SET votou = false WHERE votou = true';
        $this->db->executeUpdate($sql);
    }
}
