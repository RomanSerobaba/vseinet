<?php

namespace OrgBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use OrgBundle\Bus\Complaint\Query;
use OrgBundle\Bus\Complaint\Command;

/**
 * @VIA\Section("Жалобы директору")
 */
class ComplaintController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/complaint/",
     *     description="Получить список жалоб",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Complaint\Query\GetIndexQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Bus\Complaint\Query\DTO\Complaint"),
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
     *     path="/complaint/{id}/check/",
     *     description="Жалоба обработана",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Complaint\Command\CheckComplaintCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function checkAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\CheckComplaintCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Post(
     *     path="/complaint/{id}/comment/",
     *     description="Добавить комментарий к жалобе",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Complaint\Command\AddCommentCommand")
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
