<?php 

namespace PromoBundle\Bus\ProductReview\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use PromoBundle\Entity\ProductReview;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckProductReviewCommandHandler extends MessageHandler
{
    public function handle(CheckProductReviewCommand $command)
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

        if (boolval($command->isCheck)) {
            $productReview->setApprovedAt(new \DateTime());
            $productReview->setApprovedBy($currentUserId);
        } else {
            $productReview->setApprovedAt(null);
            $productReview->setApprovedBy(null);
        }

        $em->persist($productReview);
        $em->flush();
    }
}