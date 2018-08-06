<?php

namespace AccountingBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AccountingBundle\Bus\Clients\Query;
use AccountingBundle\Bus\Clients\Command;

/**
 * @VIA\Section("Клиенты")
 */
class ClientsController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/clients/",
     *     description="Получение списка клиентов",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="AccountingBundle\Bus\Clients\Query\GetClientsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="AppBundle\Entity\User")
     *     }
     * )
     */
    public function indexAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetClientsQuery($request->query->all()), $clients);

        return $clients;
    }

    /**
     * @VIA\Get(
     *     path="/clients/{id}/",
     *     description="Получение клиента",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="AccountingBundle\Bus\Clients\Query\GetClientQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", name="cities"),
     *         @VIA\Property(type="array", name="user")
     *     }
     * )
     */
    public function getAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetClientQuery($request->query->all(), ['id' => $id,]), $client);

        return $client;
    }

    /**
     * @VIA\Patch(
     *     path="/clients/{id}/",
     *     description="Обновить клиента",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AccountingBundle\Bus\Clients\Command\SaveClientCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function updateClientAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SaveClientCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Post(
     *     path="/clients/{id}/",
     *     description="Добавить клиента",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AccountingBundle\Bus\Clients\Command\SaveClientCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=200
     * )
     */
    public function addClientAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\SaveClientCommand($request->request->all()));
    }
}
