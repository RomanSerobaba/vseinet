<?php 

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ContentBundle\Bus\SupplierPricelist\Query;
use ContentBundle\Bus\SupplierPricelist\Command;

/**
 * @VIA\Section("Загрузка прайслиcтов")
 */
class SupplierPricelistController extends RestController 
{
    /**
     * @VIA\Get(
     *     path="/suppliers/forSupplierPricelists/",
     *     description="Получение списка поставщиков",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierPricelist\Query\GetSuppliersQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\SupplierPricelist\Query\DTO\Supplier")
     *     }
     * )
     */
    public function getSuppliersAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetSuppliersQuery($request->query->all()), $suppliers);

        return $suppliers;
    } 

    /**
     * @VIA\Get(
     *     path="/supplierPricelists/forLoaded/",
     *     description="Получение активных загрузок",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierPricelist\Query\GetLoadedQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\SupplierPricelist\Query\DTO\Loaded")
     *     }
     * )
     */
    public function getLoadedAction()
    {
        $this->get('query_bus')->handle(new Query\GetLoadedQuery(), $loaded);

        return $loaded;
    } 

    /**
     * @VIA\Get(
     *     path="/supplierPricelists/",
     *     description="Получение прайслистов поставщика",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierPricelist\Query\GetListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\SupplierPricelist\Query\DTO\Pricelist")
     *     }
     * )
     */
    public function getListAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetListQuery($request->query->all()), $pricelists);

        return $pricelists;
    }

    /**
     * @VIA\Post(
     *     path="/supplierPricelists/",
     *     description="Создание прайслиста",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierPricelist\Command\CreateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function newAction(Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\CreateCommand($request->request->all(), ['uuid' => $uuid]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid),
        ];
    }

    /**
     * @VIA\Put(
     *     path="/supplierPricelists/{id}/",
     *     description="Редактирование прайслиста",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierPricelist\Command\UpdateCommand")
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
     * @VIA\Put(
     *     path="/supplierPricelists/{id}/isActive/",
     *     description="Показывать / скрывать прайслист",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierPricelist\Command\SetIsActiveCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function setIsActiveAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetIsActiveCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Delete(
     *     path="/supplierPricelists/{id}/",
     *     description="Удаление прайслиста",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierPricelist\Command\DeleteCommand")
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

    /**
     * @VIA\Post(
     *     path="/supplierPricelists/{id}/file/",
     *     description="Загрузка прайслиста",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierPricelist\Command\UploadCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function uploadAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UploadCommand([
            'id' => $id,
            'pricelist' => $request->files->get('pricelist'),
        ]));
    }

    /**
     * @VIA\Purge(
     *     path="/supplierPricelists/{id}/",
     *     description="Сброс загрузки прайслиста",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierPricelist\Command\ResetCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function resetAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\ResetCommand(['id' => $id]));
    }
}