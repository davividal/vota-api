<?php

namespace Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EleitoresController extends BaseController
{
    public function index()
    {
        $repo = $this->getRepository('Eleitor');
        return $this->response($repo->findAll());
    }

    public function login()
    {
        $data = json_decode($this->request->getContent());

        $titulo = $data->titulo;
        $senha = $data->senha;

        $repo = $this->getRepository('Eleitor');

        /** @var Domain\Model\Eleitor $eleitor */
        $eleitor = $repo->find($titulo);

        if ($eleitor->senhaValida($senha)) {
            $code = 200;
        } else {
            $code = 401;
        }

        return $this->response(null, $code);
    }

    public function register()
    {
        $titulo = $this->request->get('titulo');
        $senha = $this->request->get('senha');

        if ($this->getRepository('Eleitor')->register($titulo, $senha)) {
            $return = $this->getRepository('Eleitor')->find($titulo);
            $code = 201;
        } else {
            $return = new \Domain\Model\Eleitor(null, null);
            $code = 422;
        }

        return $this->response($return, $code);
    }
}