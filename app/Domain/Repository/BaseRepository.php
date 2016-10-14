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
}
