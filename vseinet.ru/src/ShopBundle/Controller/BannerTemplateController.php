<?php

namespace ShopBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ShopBundle\Bus\BannerTemplate\Query;
use ShopBundle\Bus\BannerTemplate\Command;

/**
 * @VIA\Section("Магазин:Шаблоны баннеров")
 */
class BannerTemplateController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/promotion/template/",
     *     description="Список шаблонов для баннеров",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\BannerTemplate\Query\GetListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ShopBundle\Bus\BannerTemplate\Query\DTO\BannerTemplates", type="array")
     *     }
     * )
     */
    public function getListAction()
    {
        $this->get('query_bus')->handle(new Query\GetListQuery(), $list);

        return $list;
    }

    /**
     * @VIA\Delete(
     *     path="/promotion/template/{id}/",
     *     description="Удалить шаблон",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\BannerTemplate\Command\DeleteCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function deleteAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand(['id' => $id,]));
    }

    /**
     * @VIA\Post(
     *     path="/promotion/template/",
     *     description="Создать шаблон",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\BannerTemplate\Command\CreateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function createAction(Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\CreateCommand($request->request->all(), ['uuid' => $uuid,]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid),
        ];
    }

    /**
     * @VIA\Get(
     *     path="/promotion/template/{id}/",
     *     description="Данные шаблона",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\BannerTemplate\Query\GetTemplateQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ShopBundle\Entity\BannerMainTemplate", type="object")
     *     }
     * )
     */
    public function getTemplateAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetTemplateQuery(['id' => $id,]), $list);

        return $list;
    }

    /**
     * @VIA\Post(
     *     path="/promotion/template/{id}/",
     *     description="Редактировать шаблон",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\BannerTemplate\Command\EditCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function editAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\EditCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Post(
     *     path="/promotion/uploadImage/",
     *     description="Загрузить изображение",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\BannerTemplate\Command\UploadCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function uploadAction(Request $request)
    {
        if (!empty($request->files->get('file'))) {
            $ext = pathinfo($request->files->get('file')->getClientOriginalName(), PATHINFO_EXTENSION);
            $filename = md5(uniqid()) . '.' . $ext;

            $this->get('command_bus')->handle(new Command\UploadCommand([
                'filename' => $filename,
                'file' => $request->files->get('file'),
            ]));

            return $this->getParameter('banner.images.web.path') . DIRECTORY_SEPARATOR . $filename;
        }
    }
}