<?php

namespace ReservesBundle\Controller;

use AppBundle\Controller\RestController;
use ServiceBundle\Components\Utils;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ReservesBundle\Bus\Inventory\Query;
use ReservesBundle\Bus\Inventory\Command;

/**
 * @VIA\Section("Инвентаризация")
 */
class InventoryController extends RestController
{

    /**
     * @VIA\Get(
     *     path="/inventories/",
     *     description="Получить список инвентаризаций",
      parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\Inventory\Query\ListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ReservesBundle\Bus\Inventory\Query\DTO\DocumentList")
     *     }
     * )
     */
    public function listAction(Request $request)
    {

        $this->get('query_bus')->handle(new Query\ListQuery($request->query->all()), $items);

        return $items;

    }

    /**
     * @VIA\Post(
     *     path="/inventories/",
     *     description="Создать документ инвентаризации",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\Inventory\Command\CreateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */    public function createAction(Request $request)
    {

        $document = $this->get('document.Inventory');
        return ['id' => $document
                ->create(new Command\CreateCommand($request->request->all(), [
                    'createdBy' => $this->getUser()->getId()]))];

    }

    /**
     * @VIA\Get(
     *     path="/inventories/{id}/",
     *     description="Получить шапку инвентаризации",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\Inventory\Query\ItemQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ReservesBundle\Bus\Inventory\Query\DTO\InventoryItem")
     *     }
     * )
     */
    public function getAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\ItemQuery(['id' => $id]), $item);
        return $item;
    }

    /**
     * @VIA\Put(
     *     path="/inventories/{id}/",
     *     description="Изменить документ инвентаризации",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\Inventory\Command\UpdateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201
     * )
     */
    public function setAction(int $id, Request $request)
    {
        $document = $this->get('document.Inventory');
        $document->update(new Command\UpdateCommand($request->request->all(), [
            'id' => $id
        ]));
    }

    /**
     * @VIA\Delete(
     *     path="/inventories/{id}/",
     *     description="Удалить документ инвентаризации",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\Inventory\Command\DeleteCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function delAction(int $id)
    {
        $document = $this->get('document.Inventory');
        $document->delete($id);        
    }

    /**
     * @VIA\Put(
     *     path="/inventories/{id}/isCompleted/",
     *     description="Закрытие (завершение), открытие документа для редактирования.",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\Inventory\Command\CompletedCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201
     * )
     */
    public function completedAction(int $id, Request $request)
    {
        $document = $this->get('document.DocInventory');
        $document->setCompleted($id, $request->get('completed'));
    }

    /**
     * @VIA\Get(
     *     path="/inventories/{id}/printedForm/",
     *     description="Форма инвентаризационной описи",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ReservesBundle\Bus\Inventory\Command\PrintCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="file")
     *     }
     * )
     */
    public function printAction(int $id, Request $request)
    {
        $name = 'inventory_form_'.$id;
        if (!empty($request->query->get('formName'))) {
            $name .= '_'.Utils::translitIt($request->query->get('formName'));
        }
        $name .= '_'.date('d.m.Y');

        $fileName = $this->getParameter('pdf.documents.inventories.path') . DIRECTORY_SEPARATOR . $name . '.pdf';

        $this->get('command_bus')->handle(new Command\PrintCommand($request->query->all(), ['fileName' => $fileName, 'id' => $id,]));

        return $this->file($fileName);
    }
}
