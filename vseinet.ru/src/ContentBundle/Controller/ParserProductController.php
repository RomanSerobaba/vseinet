<?php 

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ContentBundle\Bus\ParserProduct\Query;
use ContentBundle\Bus\ParserProduct\Command;

/**
 * @VIA\Section("Парсеры")
 */
class ParserProductController extends RestController
{
    /**
     * @VIA\Unlink(
     *     path="/parserProducts/{id}/baseProduct/",
     *     description="Открепление товара парсера от товара сайта",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\ParserProduct\Command\UnlinkCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function detachAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UnlinkCommand($request->query->all(), ['id' => $id]));
    }
}