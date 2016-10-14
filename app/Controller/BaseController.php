<?php

namespace Controller;

use Domain\Infrastructure\Database;

class BaseController
{
    private $app;
    private $database;

    public function __construct($app)
    {
        $this->app = $app;
        $this->database = Database::getInstance($app);
    }

    public function getRepository($repository)
    {
        $repository = sprintf('Domain\Repository\%s', $repository);
        return new $repository($this->database);
    }
}
