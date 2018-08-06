<?php

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use ContentBundle\Bus\CategorySeo\Command;
use AppBundle\Annotation as VIA;

/**
 * @VIA\Section("Категории сайта")
 */
class CategorySeoController extends RestController
{
    /**
     * @VIA\Post(
     *     path="/categorySeo/",
     *     description="Создание SEO категории",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\CategorySeo\Command\CreateCommand")
     *     }
     * )
     * @VIA\Response(
     *      status=201,
     *      properties={
     *          @VIA\Property(name="id", type="integer")
     *      }
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
     *     path="/categorySeo/{id}/",
     *     description="Редактирование SEO категории",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Category\Command\UpdateCommand")
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
     *     path="/categorySeo/{id}/",
     *     description="Удаление SEO категории",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\CategorySeo\Command\DeleteCommand")
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