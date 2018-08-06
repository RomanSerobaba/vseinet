<?php

namespace PromoBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use PromoBundle\Bus\ProductReview\Query;
use PromoBundle\Bus\ProductReview\Command;

/**
 * @VIA\Section("Отзывы")
 */
class ProductReviewController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/review/",
     *     description="Получить список отзывов",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="PromoBundle\Bus\ProductReview\Query\GetIndexQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="PromoBundle\Bus\ProductReview\Query\DTO\ProductReview"),
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
     *     path="/review/{id}/check/",
     *     description="Обзор одобрен",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="PromoBundle\Bus\ProductReview\Command\CheckProductReviewCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function checkAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\CheckProductReviewCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Patch(
     *     path="/review/{id}/comment/",
     *     description="Изменить комментарий администрации к обзору",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="PromoBundle\Bus\ProductReview\Command\EditCommentCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function editCommentAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\EditCommentCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Delete(
     *     path="/review/{id}/",
     *     description="Удалить обзор",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="PromoBundle\Bus\ProductReview\Command\DeleteProductReviewCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function deleteAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeleteProductReviewCommand($request->request->all(), ['id' => $id,]));
    }
}
