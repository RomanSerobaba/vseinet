<?php

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ContentBundle\Bus\BrandLogo\Command;

/**
 * @VIA\Section("Бренды")
 */
class BrandLogoController extends RestController
{   
    /**
     * @VIA\Post(
     *     path="/brands/{id}/logo/",
     *     description="Загрузка логотипа бренда",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BrandLogo\Command\UploadCommand")
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
            'logo' => $request->files->get('logo'),
        ]));
    }

    /**
     * @VIA\Delete(
     *     path="/brands/{id}/logo/",
     *     description="Удаление логотипа бренда",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BrandLogo\Command\DeleteCommand")
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