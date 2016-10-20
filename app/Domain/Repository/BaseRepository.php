<?php

namespace Domain\Repository;

use Domain\Infrastructure\Database;

class BaseRepository
{
    protected $db;

    public function __construct(Database $db)
    {
        $this->db = $db->getConnection();
    }

    public function beginTransaction()
    {
        $this->db->beginTransaction();
    }

    public function commit()
    {
        $this->db->commit();
    }

    public function rollBack()
    {
        $this->db->rollBack();
    }
}
