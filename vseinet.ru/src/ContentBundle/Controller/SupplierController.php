<?php

namespace ContentBundle\Controller;

// Удалить после корректировки кода
use Symfony\Component\HttpFoundation\Response;
use SupplyBundle\Entity\Supplier;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ContentBundle\Bus\Supplier\Query;
use ContentBundle\Bus\Supplier\Command;
/**
 * @VIA\Section("Поставщики")
 * @todo : переместить в SupplyBundle
 */
class SupplierController extends RestController {

    
    /**
     * @VIA\Get(
     *     path="/suppliers",
     *     description="Получить список поставщиков",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="SupplyBundle\Entity\Supplier")
     *     }
     * )
     */
    public function listAction(Request $request) {
        $this->get('query_bus')->handle(new Query\GetAllQuery(), $items);

        return $items;
    }

    /**
     * @VIA\Get(
     *     path="/supplier/{id}",
     *     description="Получить поставщика",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\Supplier\Query\GetItemQuery")   
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="SupplyBundle\Entity\Supplier")
     *     }
     * )
     */
    public function getAction(int $id) {
        $this->get('query_bus')->handle(new Query\GetItemQuery(['id' => $id]), $item);

        return $item;
    }

    /**
     * @VIA\Post(
     *     path="/supplier",
     *     description="Добавить поставщика",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\Supplier\Command\CreateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function createAction(Request $request) 
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\CreateCommand($request->request->all(), ['uuid' => $uuid]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid)
        ];
    }

    /**
     * @VIA\Put(
     *     path="/supplier/{id}",
     *     description="Обновить поставщика",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\Supplier\Command\UpdateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function updateAction(int $id, Request $request) {
        $this->get('command_bus')->handle(new Command\UpdateCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Delete(
     *     path="/supplier/{id}",
     *     description="Удалить поставщика",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\Supplier\Command\DeleteCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function delAction(int $id) {
        $this->get('command_bus')->handle(new Command\DeleteCommand(['id' => $id]));
    }

}
