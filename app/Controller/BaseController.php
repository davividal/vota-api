<?php

namespace Controller;

use Domain\Infrastructure\Database;
use Symfony\Component\HttpFoundation\Request;

class BaseController
{
    protected $app;
    protected $database;
    protected $request;

    public function __construct($app, Request $request)
    {
        $this->app = $app;
        $this->database = Database::getInstance($app);

        $this->request = $request;
    }

    public function getRepository($repository)
    {
        $repository = sprintf('Domain\Repository\%s', $repository);
        return new $repository($this->database);
    }
}
