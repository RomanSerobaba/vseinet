<?php 

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use ContentBundle\Bus\DetailValueAlias\Query;
use ContentBundle\Bus\DetailValueAlias\Command;
use AppBundle\Annotation as VIA;

/**
 * @VIA\Section("Характеристики")
 */
class DetailValueAliasController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/detailValueAliases/",
     *     description="Получение списка псевдонимов значения характеристики",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\DetailValueAlias\Query\GetListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\DetailValueAlias\Query\DTO\Alias")
     *     }
     * )
     */
    public function getListAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetListQuery($request->query->all()), $aliases);

        return $aliases;
    }

    /**
     * @VIA\Put(
     *     path="/detailValueAliases/{id}/",
     *     description="Редактирование псевдонима значения характеристики",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\DetailValueAlias\Command\UpdateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function editAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdateCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Delete(
     *     path="/detailValueAliases/{id}/",
     *     description="Удаление псевдонима значения характеристики",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\DetailValue\Command\DeleteCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function removeAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand(['id' => $id]));
    }
}