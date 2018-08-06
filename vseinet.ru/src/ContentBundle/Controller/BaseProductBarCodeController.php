<?php

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ContentBundle\Bus\BaseProductBarCode\Query;
use ContentBundle\Bus\BaseProductBarCode\Command;

/**
 * @VIA\Section("Штрихкоды")
 */
class BaseProductBarCodeController extends RestController
{    
    /**
     * @VIA\Get(
     *     path="/barcodes/",
     *     description="Получить список штрихкодов c товарами",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\BaseProductBarCode\Query\GetProductsBarCodesQuery")   
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\BaseProductBarCode\Query\DTO\ProductBarCode")
     *     }
     * )
     */
    public function listProductsBarcodesAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetProductsBarCodesQuery($request->query->all()), $items);
        return $items;
    }

    /**
     * @VIA\Get(
     *     path="/barcodes/print/",
     *     description="Печать штрихкода продукта",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\BaseProductBarCode\Query\PrintQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=204,
     *     properties={
     *         @VIA\Property(type="file")
     *     }
     * )
     */
    public function printBarcodeAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\PrintQuery($request->query->all()), $filePath);
        return $this->file($filePath);
    }

    /**
     * @VIA\Get(
     *     path="/barcodes/{barCode}/",
     *     description="Получить список товаров штрихкода",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\BaseProductBarCode\Query\GetProductsQuery")   
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\BaseProductBarCode\Query\DTO\FindByBarcodeResult")
     *     }
     * )
     */
    public function listProductsAction(string $barCode, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetProductsQuery($request->query->all(), ['barCode' => $barCode]), $items);
        return $items;
    }

    /**
     * @VIA\Post(
     *     path="/barcodes/{barCode}/",
     *     description="Добавить ширихкод продукта",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\BaseProductBarCode\Command\CreateCommand")   
     *     }
     * )
     * @VIA\Response(
     *     status=201
     * )
     */
    public function createProductBarCodeAction($barCode, Request $request)
    {
        $this->get('command_bus')->handle(new Command\CreateCommand($request->request->all(), [
            'barCode' => $barCode]));
    }

    /**
     * @VIA\Delete(
     *     path="/barcodes/{id}/",
     *     description="Удалить штрихкод продукта",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\BaseProductBarCode\Command\DeleteCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function delPdoductBarcodeAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand(['id' => $id]));
    }

}
