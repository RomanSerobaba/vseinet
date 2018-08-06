<?php

namespace OrgBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use OrgBundle\Bus\Representative\Query;
use OrgBundle\Bus\Representative\Command;

/**
 * @VIA\Section("Представительства")
 */
class RepresentativeController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/representatives/",
     *     description="Получить список представительств",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Representative\Query\GetIndexQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Entity\Representative")
     *     }
     * )
     */
    public function GetIndexAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetIndexQuery($request->query->all()), $index);

        return $index;
    }

    /**
     * @VIA\Get(
     *     path="/representatives/foundResults/",
     *     description="Поиск представительств",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Representative\Query\FoundResultsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Bus\Representative\Query\DTO\Point")
     *     }
     * )
     */
    public function FoundResultsAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\FoundResultsQuery($request->query->all()), $result);

        return $result;
    }

    /**
     * @VIA\Get(
     *     path="/org/points/",
     *     description="Получить список точек",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Representative\Query\GetRepresentativePointsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Bus\Representative\Query\DTO\RepresentativePoints")
     *     }
     * )
     */
    public function getRepresentativePointsAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetRepresentativePointsQuery($request->query->all()), $index);

        return $index;
    }

    /**
     * @VIA\Get(
     *     path="/org/representative/{id}/",
     *     description="Получить представительство",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Representative\Query\GetRepresentativeQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="OrgBundle\Entity\Representative")
     *     }
     * )
     */
    public function GetRepresentativeAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetRepresentativeQuery($request->query->all(), ['id' => $id,]), $representative);

        return $representative;
    }

    /**
     * @VIA\Patch(
     *     path="/org/representative/{id}/",
     *     description="Сохранить представительство",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Representative\Command\SaveRepresentativeCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function SaveRepresentativeAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SaveRepresentativeCommand($request->query->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Post(
     *     path="/representatives/",
     *     description="Создать представительство кратко",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Representative\Command\CreateRepresentativeShortCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function CreateRepresentativeShortAction(Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\CreateRepresentativeShortCommand($request->request->all(), ['uuid' => $uuid]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid)
        ];
    }

    /**
     * @VIA\Get(
     *     path="/representatives/{id}/",
     *     requirements={"id"="\d+"},
     *     description="Получить представительство кратко",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Representative\Query\GetRepresentativeShortQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="OrgBundle\Bus\Representative\Query\DTO\Representative")
     *     }
     * )
     */
    public function GetRepresentativeShortAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetRepresentativeShortQuery($request->query->all(), ['id' => $id,]), $representative);

        return $representative;
    }

    /**
     * @VIA\Put(
     *     path="/representatives/{id}/",
     *     requirements={"id"="\d+"},
     *     description="Сохранить представительство кратко",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Representative\Command\SaveRepresentativeShortCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function SaveRepresentativeShortAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SaveRepresentativeShortCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Post(
     *     path="/org/representative/new/",
     *     description="Создать представительство",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Representative\Command\SaveRepresentativeCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function AddRepresentativeAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\SaveRepresentativeCommand($request->query->all()));
    }

    /**
     * @VIA\Patch(
     *     path="/org/representative/{id}/setHasRetail/",
     *     description="",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="OrgBundle\Bus\Representative\Command\SetHasRetailCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function setHasRetailAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetHasRetailCommand($request->query->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Patch(
     *     path="/org/representative/{id}/setIsCentral/",
     *     description="",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="OrgBundle\Bus\Representative\Command\SetIsCentralCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function setIsCentralAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetIsCentralCommand($request->query->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Patch(
     *     path="/org/representative/{id}/setIsDefault/",
     *     description="",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="OrgBundle\Bus\Representative\Command\SetIsDefaultCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function setIsDefaultAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetIsDefaultCommand($request->query->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Post(
     *     path="/org/representative/{id}/addRoom/",
     *     description="Добавить помещение",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Representative\Command\AddRoomCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function AddRoomAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\AddRoomCommand($request->query->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Get(
     *     path="/points/forStores/",
     *     description="Получение списка точек",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="OrgBundle\Bus\Representative\Query\GetPointsQuery"),
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Bus\Representative\Query\DTO\Point")
     *     }
     * )
     */
    public function getStoresAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetPointsQuery($request->request->all()), $points);

        return $points;
    }

    /**
     * @VIA\Get(
     *     path="/points/forOrdersShipping/",
     *     description="Список наших точек",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="OrgBundle\Bus\Representative\Query\GetPointsForShippingQuery"),
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Bus\Representative\Query\DTO\PointsForShipping")
     *     }
     * )
     */
    public function getForShippingAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetPointsForShippingQuery($request->query->all()), $points);

        return $points;
    }

    /**
     * @VIA\Get(
     *     path="/representativeReserves/",
     *     description="Получить список резервов на точках",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="OrgBundle\Bus\Representative\Query\GetRepresentativeReservesQuery"),
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Bus\Representative\Query\DTO\RepresentativeReserves")
     *     }
     * )
     */
    public function getRepresentativeReservesAction()
    {
        $this->get('query_bus')->handle(new Query\GetRepresentativeReservesQuery(), $points);

        return $points;
    }

    /**
     * @VIA\Post(
     *     path="/representatives/{id}/orders/",
     *     description="Создание заявки на пополнение товарных остатков на точке",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Representative\Command\CreateResupplyOrderCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function createResupplyOrderAction(int $id, Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\CreateResupplyOrderCommand($request->request->all(), ['id' => $id, 'uuid' => $uuid,]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid),
        ];
    }
}
