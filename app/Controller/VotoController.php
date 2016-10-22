<?php

namespace Controller;

class VotoController extends BaseController
{
    public function votar()
    {
        $data = json_decode($this->request->getContent());

        $titulo = $data->titulo_eleitor;
        $vereador = $data->vereador;
        $prefeito = $data->prefeito;

        $eleitorRepo = $this->getRepository('Eleitor');
        $eleitorRepo->beginTransaction();

        /** @var Domain\Model\Eleitor $eleitor */
        $votoEleitor = $eleitorRepo->registrarVoto($titulo);

        $response = new \stdClass;

        if (!$votoEleitor) {
            $response->message = "Eleitor já votou";

            return $this->response(
                $response,
                429,
                ['Retry-After' => 4 * 365 * 86400]
            );
        }

        $vereadorRepo = $this->getRepository('Vereador');
        $votoVereador = $vereadorRepo->registrarVoto($vereador);

        $prefeitoRepo = $this->getRepository('Prefeito');
        $votoPrefeito = $prefeitoRepo->registrarVoto($prefeito);

        if ($votoVereador && $votoPrefeito) {
            $eleitorRepo->commit();
            $response->message = "Voto computado com sucesso";

            return $this->response(
                $response,
                200
            );
        } else {
            $eleitorRepo->rollBack();
            $response->message = "Erro ao votar";

            return $this->response(
                $response,
                422
            );
        }
    }

    public function resultadoPrefeitos()
    {
        $repo = $this->getRepository('Prefeito');

        return $this->response($repo->resultadoPrefeitos());
    }

    public function resultadoVereadores()
    {
        $repo = $this->getRepository('Vereador');

        return $this->response($repo->resultadoVereadores());
    }
}
