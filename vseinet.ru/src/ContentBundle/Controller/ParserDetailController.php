<?php 

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ContentBundle\Bus\ParserDetail\Command;

/**
 * @VIA\Section("Парсеры")
 */
class ParserDetailController extends RestController
{
    /**
     * @VIA\Patch(
     *     path="/parserDetails/{id}/isHidden",
     *     description="Показывать / скрывать характеристику парсера",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\ParserDetail\Command\SetIsHiddenCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function setIsHiddenAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetIsHiddenCommand($request->request->all(), ['id' => $id]));
    }  

    /**
     * @VIA\Link(
     *     path="/parserDetails/{id}/details/",
     *     description="Прикрепить характеристику парсера к характеристике на сайте",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\ParserDetail\Command\AttachCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function attachAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\AttachCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Unlink(
     *     path="/parserDetails/{id}/details/",
     *     description="Открепить характеристику парсера от характеристики на сайте",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\ParserDetail\Command\DetachCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function detachAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\DetachCommand($request->request->all(), ['id' => $id]));
    }
}
