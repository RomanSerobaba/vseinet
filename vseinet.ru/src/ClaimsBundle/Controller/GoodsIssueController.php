<?php

namespace ClaimsBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ClaimsBundle\Bus\GoodsIssue\Query;
use ClaimsBundle\Bus\GoodsIssue\Command;

/**
 * @VIA\Section("Претензии")
 */
class GoodsIssueController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/goodsIssue/index/",
     *     description="Получение списка претензий",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ClaimsBundle\Bus\GoodsIssue\Query\GetGoodsIssuesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="intereg", name="balance"),
     *         @VIA\Property(type="intereg", name="total"),
     *         @VIA\Property(type="array", name="list", model="ClaimsBundle\Entity\GoodsIssue"),
     *         @VIA\Property(type="array", name="types"),
     *         @VIA\Property(type="array", name="rooms"),
     *         @VIA\Property(type="array", name="suppliers"),
     *         @VIA\Property(type="array", name="services")
     *     }
     * )
     */
    public function indexAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetGoodsIssuesQuery($request->query->all()), $issues);

        return $issues;
    }

    /**
     * @VIA\Get(
     *     path="/goodsIssue/{id}/",
     *     description="Получение информации по претензии",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ClaimsBundle\Bus\GoodsIssue\Query\GetGoodsIssueQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ClaimsBundle\Bus\GoodsIssue\Query\GetGoodsIssueQuery")
     *     }
     * )
     */
    public function getAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetGoodsIssueQuery($request->query->all(), ['id' => $id,]), $item);

        return $item;
    }

    /**
     * @VIA\Patch(
     *     path="/goodsIssue/{id}/description/",
     *     description="Изменение описание проблемы",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ClaimsBundle\Bus\GoodsIssue\Command\UpdateDescriptionCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function descriptionAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdateDescriptionCommand($request->query->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Post(
     *     path="/goodsIssue/{id}/comment/",
     *     description="Добавление комментария к претензии",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ClaimsBundle\Bus\GoodsIssue\Command\AddCommentCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=200
     * )
     */
    public function commentAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\AddCommentCommand($request->query->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Patch(
     *     path="/goodsIssue/{id}/accepted/",
     *     description="Простановка признака 'Принят менеджером по браку'",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ClaimsBundle\Bus\GoodsIssue\Command\AcceptedCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function acceptedAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\AcceptedCommand($request->query->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Patch(
     *     path="/goodsIssue/{id}/type/",
     *     description="Изменение 'Тип претензии'",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ClaimsBundle\Bus\GoodsIssue\Command\TypeCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function typeAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\TypeCommand($request->query->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Patch(
     *     path="/goodsIssue/{id}/compensation/",
     *     description="Установка 'Ожидаемая сумма компенсации'",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ClaimsBundle\Bus\GoodsIssue\Command\SetCompensationCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function compensationAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetCompensationCommand($request->query->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Patch(
     *     path="/goodsIssue/{id}/cancelCompensation/",
     *     description="Установка 'Отменить ожидание компенсации'",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ClaimsBundle\Bus\GoodsIssue\Command\SetCompensationCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function cancelCompensationAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetCompensationCommand($request->query->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Patch(
     *     path="/goodsIssue/{id}/supplierDecision/",
     *     description="Установка 'Подтвердить получение компенсации от поставщика'",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ClaimsBundle\Bus\GoodsIssue\Command\SetSupplierDecisionCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function supplierDecisionAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetSupplierDecisionCommand($request->query->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Patch(
     *     path="/goodsIssue/{id}/supplier/",
     *     description="Выбор поставщика для 'Решение по товару'",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ClaimsBundle\Bus\GoodsIssue\Command\SetSupplierCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function supplierAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetSupplierCommand($request->query->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Patch(
     *     path="/goodsIssue/{id}/productDecision/",
     *     description="Установка 'Решение по товару'",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ClaimsBundle\Bus\GoodsIssue\Command\SetProductDecisionCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function productDecisionAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetProductDecisionCommand($request->query->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Patch(
     *     path="/goodsIssue/{id}/clientDecision/",
     *     description="Установка 'Решение по клиенту'",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ClaimsBundle\Bus\GoodsIssue\Command\SetClientDecisionCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function clientDecisionAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetClientDecisionCommand($request->query->all(), ['id' => $id,]));
    }


    /**
     * @VIA\Patch(
     *     path="/goodsIssue/{id}/accept/",
     *     description="Установка 'Подтвердить приемку товара на склад'",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ClaimsBundle\Bus\GoodsIssue\Command\AcceptCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function acceptAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\AcceptCommand($request->query->all(), ['id' => $id,]));
    }
}
