<?php

namespace SupplyBundle\Controller;

use AppBundle\Controller\RestController;
use SupplyBundle\Entity\Supplier;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use SupplyBundle\Bus\Reserves\Query;
use SupplyBundle\Bus\Reserves\Command;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @VIA\Section("Товарные остатки поставки")
 */
class ReservesController extends RestController
{
    /**
     * @VIA\Post(
     *     path="/suppliers/{id}/ordersProcessingFile/",
     *     description="Загрузка счета",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Reserves\Query\UploadQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function uploadReserveAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\UploadQuery($request->query->all(), [
            'filename' => $request->files->get('filename'),
            'id' => $id,
        ]), $list);

        return $list;
    }

    /**
     * @VIA\Patch(
     *     path="/supplierReserves/{id}/items/",
     *     description="Сохранение резерва поставщика",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Reserves\Command\ReserveSupplierConfirmationCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function confirmSupplierReserveAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\ReserveSupplierConfirmationCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Get(
     *     path="/supplierReserves/{id}/items/",
     *     description="Получение списка необработанных позиций по поставщику",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Reserves\Query\GetIndexQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(name="index", model="SupplyBundle\Bus\Order\Query\DTO\Order")
     *     }
     * )
     */
    public function getProcessingItemsAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetIndexQuery($request->query->all(), ['id' => $id,]), $index);

        return $index;
    }

    /**
     * @VIA\Get(
     *     path="/suppliers/{id}/ordersProcessingFile/",
     *     description="Выгрузка запроса поставщику в выбранном формате",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Reserves\Command\DownloadCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="file")
     *     }
     * )
     */
    public function downloadRequestAction(int $id, Request $request)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $format = $request->query->get('format');

        $supplier = $em->getRepository(Supplier::class)->find($id);
        if (!$supplier instanceof Supplier) {
            throw new NotFoundHttpException();
        }

        switch ($format) {
            case Command\DownloadCommand::FORMAT_STRING:
                $ext = 'txt.html';
                break;

            case Command\DownloadCommand::FORMAT_HTML:
                $ext = 'html';
                break;

            case Command\DownloadCommand::FORMAT_CSV:
                $ext = 'csv';
                break;

            case Command\DownloadCommand::FORMAT_EXCEL:
                $ext = 'xls';
                break;

            default:
                throw new BadRequestHttpException('Unknown format: '.$format);
        }

        $pointId = $request->query->get('pointId');
        $withConfirmedReserves = $request->query->get('withConfirmedReserves');

        $name = 'export_for_'.$supplier->getCode();
        if (!empty($pointId)) {
            $name .= '_p'.$pointId;
        }
        if (!empty($withConfirmedReserves)) {
            $name .= '_reserved';
        }
        $name .= '_'.date('d.m.Y');

        $fileName = $this->getParameter('pdf.documents.orders.order.path') . DIRECTORY_SEPARATOR . $name . '.' . $ext;

        if (!file_exists($this->getParameter('pdf.documents.orders.order.path'))) {
            mkdir($this->getParameter('pdf.documents.orders.order.path'), 0777, true);
        }

        $this->get('command_bus')->handle(new Command\DownloadCommand($request->query->all(), ['fileName' => $fileName, 'id' => $id,]));

        return $this->file($fileName);
    }
}