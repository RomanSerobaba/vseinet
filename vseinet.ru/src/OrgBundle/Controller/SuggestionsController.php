<?php

namespace OrgBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use OrgBundle\Bus\Suggestions\Query;
use OrgBundle\Bus\Suggestions\Command;

/**
 * @VIA\Section("Книга предложений")
 */
class SuggestionsController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/suggestions/",
     *     description="Получить список предложений",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Suggestions\Query\GetIndexQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", name="items", model="OrgBundle\Bus\Suggestions\Query\DTO\Suggestion"),
     *         @VIA\Property(type="integer", name="pageCount")
     *     }
     * )
     */
    public function indexAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetIndexQuery($request->query->all()), $index);

        return $index;
    }

    /**
     * @VIA\Patch(
     *     path="/suggestions/{id}/check/",
     *     description="Предложение обработано",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Suggestions\Command\CheckSuggestionsCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function checkAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\CheckSuggestionsCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Post(
     *     path="/suggestions/{id}/comment/",
     *     description="Добавить комментарий к предложению",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Suggestions\Command\AddCommentCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function addCommentAction(int $id, Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\AddCommentCommand($request->request->all(), ['id' => $id, 'uuid' => $uuid,]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid),
        ];
    }
}
