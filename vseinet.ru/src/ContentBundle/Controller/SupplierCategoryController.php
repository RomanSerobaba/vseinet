<?php 

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ContentBundle\Bus\SupplierCategory\Command;

/**
 * @VIA\Section("Категории товаров поставщиков")
 */
class SupplierCategoryController extends RestController 
{
    /**
     * @VIA\Put(
     *     path="/supplierCategories/{id}/isHidden/",
     *     description="Показать / скрыть категорию поставщика",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierCategory\Command\SetIsHiddenCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function setIsHiddenAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetIsHiddenCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Link(
     *     path="/supplierCategories/{id}/categories/",
     *     description="Синхронизация категории поставщика с категорией на сайте",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierCategory\Command\SynchronizeCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function synchronizeAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SynchronizeCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Unlink(
     *     path="/supplierCategories/{id}/categories/",
     *     description="Разрыв синхронизации категории поставщика с категорией на сайте",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierCategory\Command\SynchronizeCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function resynchronizeAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\ResynchronizeCommand($request->request->all(), ['id' => $id]));
    }
}