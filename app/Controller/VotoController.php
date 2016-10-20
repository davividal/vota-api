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

        if (!$votoEleitor) {
            return $this->response(
                ["Eleitor já votou"],
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
            return $this->response(["Voto computado com sucesso"], 200);
        } else {
            $eleitorRepo->rollBack();
            return $this->response(["Erro ao votar"], 422);
        }
    }
}
