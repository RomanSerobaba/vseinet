<?php 

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ContentBundle\Bus\SupplierProduct\Command;

/**
 * @VIA\Section("Товары поставщиков")
 */
class SupplierProductController extends RestController 
{
    /**
     * @VIA\Post(
     *     path="/supplierProducts/",
     *     description="Перенос товаров поставщика в базу",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierProduct\Command\TransferCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function transferAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\TransferCommand($request->request->all()));
    }

    /**
     * @VIA\Link(
     *     path="/supplierProducts/baseProducts/",
     *     description="Прикрепление товаров поставщика к существующему на сайте",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierProduct\Command\AttachCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function attachAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\AttachCommand($request->request->all()));
    }

    /**
     * @VIA\Unlink(
     *     path="/supplierProducts/{id}/baseProduct/",
     *     description="Открепление товара поставщика от товара сайта",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierProduct\Command\DetachCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function detachAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\DetachCommand($request->query->all(), ['id' => $id]));
    }


    /**
     * @VIA\Put(
     *     path="/supplierProducts/isHidden/",
     *     description="Скрытие товаров поставщика",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierProduct\Command\SetIsHiddenCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function setIsHiddenAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetIsHiddenCommand($request->request->all()));
    }

    /**
     * @VIA\Delete(
     *     path="/supplierProducts/",
     *     description="Удаление товаров поставщика",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierProduct\Command\DeleteCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function removeAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand($request->request->all()));
    }
}