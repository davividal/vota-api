<?php

namespace Controller;

use Domain\Model\Eleitor;
use Domain\Repository\Prefeito;
use Domain\Repository\Vereador;

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

        /** @var \Domain\Repository\Eleitor $eleitor */
        $votoEleitor = $eleitorRepo->registrarVoto($titulo);

        $response = new \stdClass;

        if (!$votoEleitor) {
            $response->message = "Eleitor já votou";

            // https://httpstatuses.com/429
            // 4 * 365 * 86400 = 4 anos, em segundos
            // TOO MANY REQUESTS
            return $this->response(
                $response,
                429,
                ['Retry-After' => 4 * 365 * 86400]
            );
        }

        /** @var Vereador $vereadorRepo */
        $vereadorRepo = $this->getRepository('Vereador');
        $votoVereador = $vereadorRepo->registrarVoto($vereador);

        /** @var Prefeito $prefeitoRepo */
        $prefeitoRepo = $this->getRepository('Prefeito');
        $votoPrefeito = $prefeitoRepo->registrarVoto($prefeito);

        if ($votoVereador && $votoPrefeito) {
            $eleitorRepo->commit();
            $response->message = "Voto computado com sucesso";

            // https://httpstatuses.com/200
            // OK
            return $this->response(
                $response,
                200
            );
        } else {
            $eleitorRepo->rollBack();
            $response->message = "Erro ao votar";

            // https://httpstatuses.com/422
            // UNPROCESSABLE ENTITY
            return $this->response(
                $response,
                422
            );
        }
    }

    public function resultadoPrefeitos($prefeitoId)
    {
        /** @var Prefeito $repo */
        $repo = $this->getRepository('Prefeito');

        if ($prefeitoId) {
            return $this->response($repo->resultadoPrefeito($prefeitoId));
        } else {
            return $this->response($repo->resultadoPrefeitos());
        }
    }

    public function resultadoVereadores($vereadorId)
    {
        /** @var Vereador $repo */
        $repo = $this->getRepository('Vereador');

        if ($vereadorId) {
            return $this->response($repo->resultadoVereador($vereadorId));
        } else {
            return $this->response($repo->resultadoVereadores());
        }
    }

    public function novaEleicao()
    {
        /** @var \Domain\Repository\Eleitor $eleitorRepo */
        $eleitorRepo = $this->getRepository('Eleitor');
        $eleitorRepo->novaEleicao();

        /** @var Prefeito $prefeitoRepo */
        $prefeitoRepo = $this->getRepository('Prefeito');
        $prefeitoRepo->novaEleicao();

        /** @var Vereador $vereadorRepo */
        $vereadorRepo = $this->getRepository('Vereador');
        $vereadorRepo->novaEleicao();

        return $this->response(null);
    }
}
