<?php

namespace Controller;

use Domain\Model\Eleitor;


class EleitoresController extends BaseController
{
    public function index()
    {
        /** @var \Domain\Repository\Eleitor $eleitorRepo */
        $eleitorRepo = $this->getRepository('Eleitor');

        return $this->response($eleitorRepo->findAll());
    }

    public function login()
    {
        $data = json_decode($this->request->getContent());

        $titulo = $data->titulo;
        $senha = $data->senha;

        /** @var \Domain\Repository\Eleitor $eleitorRepo */
        $eleitorRepo = $this->getRepository('Eleitor');

        $eleitor = $eleitorRepo->find($titulo);

        if ($eleitor->senhaValida($senha)) {
            // https://httpstatuses.com/200
            // OK
            $code = 200;
        } else {
            // https://httpstatuses.com/401
            // Unauthorized
            $code = 401;
        }

        return $this->response(null, $code);
    }

    public function register()
    {
        $titulo = $this->request->get('titulo');
        $senha = $this->request->get('senha');

        /** @var \Domain\Repository\Eleitor $eleitorRepo */
        $eleitorRepo = $this->getRepository('Eleitor');
        if ($eleitorRepo->register($titulo, $senha)) {
            $return = $eleitorRepo->find($titulo);
            // https://httpstatuses.com/201
            // CREATED
            $code = 201;
        } else {
            $return = new Eleitor(null, null);
            // https://httpstatuses.com/422
            // UNPROCESSABLE ENTITY
            $code = 422;
        }

        return $this->response($return, $code);
    }

    public function registerBatch()
    {
        $eleitores = json_decode($this->request->getContent());

        /** @var \Domain\Repository\Eleitor $eleitorRepo */
        $eleitorRepo = $this->getRepository('Eleitor');
        foreach ($eleitores as $eleitor) {
            $eleitorRepo->register($eleitor->titulo, $eleitor->senha);
        }

        // https://httpstatuses.com/201
        // CREATED
        return $this->response(null, 201);
    }
}
