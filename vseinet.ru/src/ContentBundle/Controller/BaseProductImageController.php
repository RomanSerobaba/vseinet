<?php 

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ContentBundle\Bus\BaseProductImage\Query;
use ContentBundle\Bus\BaseProductImage\Command;

/**
 * @VIA\Section("Изображения товаров")
 */
class BaseProductImageController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/baseProductImages/{id}/",
     *     description="Получение изображения",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProductImage\Query\GetQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ContentBundle\Entity\BaseProductImage")
     *     }
     * )
     */
    public function getAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetQuery($request->query->all(), ['id' => $id]), $image);

        return $image;
    }
    
    /**
     * @VIA\Post(
     *     path="/baseProductImages/file/",
     *     description="Загрузка изображения товара",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProductImage\Command\UploadCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function uploadAction(Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\UploadCommand([
            'baseProductId' => $request->request->get('baseProductId'), 
            'image' => $request->files->get('image'),
            'uuid' => $uuid,
        ]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid),
        ];
    }

    /**
     * @VIA\Put(
     *     path="/baseProductImages/{id}/sortOrder/",
     *     description="Сортировка изображений",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProductImage\Command\SortCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function sortAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SortCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Delete(
     *     path="/baseProductImages/{id}/",
     *     description="Удаление изображения",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProductImage\Command\DeleteCommand")
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