<?php 

namespace OrderBundle\Bus\Item\Query;

use AppBundle\Bus\Message\MessageHandler;
use OrderBundle\Entity\OrderItem;
use ServiceBundle\Services\OrderService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetCommentsQueryHandler extends MessageHandler
{
    /**
     * @param GetCommentsQuery $query
     *
     * @return array
     */
    public function handle(GetCommentsQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();
        /**
         * @var OrderItem $model
         */
        $model = $em->getRepository(OrderItem::class)->find($query->id);

        if (!$model instanceof OrderItem) {
            throw new NotFoundHttpException();
        }

        $list = [];
        /**
         * @var OrderService $orderService
         */
        $orderService = $this->get('service.order');
        $comments = $orderService->getOrderItemComments($query->id, false);

        foreach ($comments as $comment) {
            if (!empty($comment->isCommon) || (!empty($comment->orderItemId) && $comment->orderItemId == $query->id)) {
                $list[] = $comment;
            }
        }

        return $list;
    }
}