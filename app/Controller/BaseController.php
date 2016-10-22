<?php

namespace Controller;

use Domain\Infrastructure\Database;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

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

    /*
     * @param $data mixed The data that should be returned
     * @param $code integer The HTTP status code
     * @return JsonResponse
     **/
    public function response($data, $code = 200, array $headers = [])
    {
        return new JsonResponse($data, $code, $headers);
    }
}
