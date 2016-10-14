<?php

namespace Controller;

use Symfony\Component\HttpFoundation\Request;

class VotersController extends BaseController
{
    public function index()
    {
        $repo = $this->getRepository('Eleitor');
        return $repo->findAll();
    }

    public function login(Request $request)
    {
        $data = json_decode($request->getContent());

        $titulo = $data->titulo;
        $senha = $data->senha;

        $repo = $this->getRepository('Eleitor');

        /** @var Domain\Model\Eleitor $eleitor */
        $eleitor = $repo->find($titulo);

        if ($eleitor->senhaValida($senha)) {
            return 'Login correto';
        } else {
            return 'Login incorreto';
        }
    }

    public function register(Request $request)
    {
        $titulo = $request->get('titulo');
        $senha = $request->get('senha');

        if ($this->getRepository('Eleitor')->register($titulo, $senha)) {
            return 'Eleitor cadastrado';
        } else {
            return 'Erro ao cadastrar eleitor';
        }
    }
}
