<?php

namespace DocumentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use DocumentBundle\Bus\Comment\Query;
use DocumentBundle\Bus\Comment\Command;

/**
 * @VIA\Section("Комментарии к документам")
 */
class StatusController extends RestController
{

    /**
     * @VIA\Get(
     *     path="/statuses/{documentType}/",
     *     description="Получить список",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="DocumentBundle\Bus\Comment\Query\ListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="DocumentBundle\Bus\Comment\Query\DTO\Comment")
     *     }
     * )
     */
    public function listAction(Request $request)
    {

        $this->get('query_bus')->handle(new Query\ListQuery($request->query->all()), $items);

        return $items;

    }

    /**
     * @VIA\Get(
     *     path="/statuses/{documentType}/{codeid}/",
     *     description="Получить комментарий",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="DocumentBundle\Bus\Comment\Query\ItemQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="DocumentBundle\Bus\Comment\Query\DTO\Comment")
     *     }
     * )
     */
    public function getAction(int $id)
    {
        
        $this->get('query_bus')->handle(new Query\ItemQuery([
            'id' => $id,
        ]), $item);
        
        return $item;
        
    }

    /**
     * @VIA\Put(
     *     path="/comments/{id}/",
     *     description="Изменить комментарий",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="DocumentBundle\Bus\Comment\Command\UpdateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201
     * )
     */
    public function setAction(int $id, Request $request)
    {
        
        $this->get('command_bus')->handle(new Command\UpdateCommand($request->request->all(), [
            'id' => $id,
        ]));
        
    }

    /**
     * @VIA\Delete(
     *     path="/comments/{id}/",
     *     description="Удалить комментарий",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="DocumentBundle\Bus\Comment\Command\DeleteCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function delAction(int $id)
    {
        
        $this->get('command_bus')->handle(new Command\DeleteCommand([
            'id' => $id,
        ]));
        
    }

}
