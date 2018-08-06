<?php 

namespace PromoBundle\Bus\ProductReview\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use PromoBundle\Entity\ProductReview;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteProductReviewCommandHandler extends MessageHandler
{
    public function handle(DeleteProductReviewCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();
        $currentUserId = $currentUser->getId();

        /**
         * @var ProductReview $productReview
         */
        $productReview = $em->getRepository(ProductReview::class)->find($command->id);

        if (!$productReview) {
            throw new NotFoundHttpException('Отзыв не найден');
        }

        if (!empty($productReview->getDeletedAt())) {
            throw new BadRequestHttpException('Отзыв уже удален');
        }

        $productReview->setDeletedAt(new \DateTime());
        $productReview->setDeletedBy($currentUserId);

        $em->persist($productReview);
        $em->flush();
    }
}