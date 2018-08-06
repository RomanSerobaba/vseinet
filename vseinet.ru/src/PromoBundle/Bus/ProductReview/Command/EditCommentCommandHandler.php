<?php 

namespace PromoBundle\Bus\ProductReview\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\ResultSetMapping;
use http\Exception\BadQueryStringException;
use OrgBundle\Entity\Complaint;
use OrgBundle\Entity\ComplaintComment;
use OrgBundle\Entity\GeoPoint;
use OrgBundle\Entity\GeoRoom;
use OrgBundle\Entity\Representative;
use OrgBundle\Entity\RepresentativePhoto;
use OrgBundle\Entity\RepresentativeSchedule;
use PromoBundle\Entity\ProductReview;
use ServiceBundle\Components\Image;
use ServiceBundle\Components\Utils;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ThirdPartyBundle\Entity\GeoAddress;

class EditCommentCommandHandler extends MessageHandler
{
    public function handle(EditCommentCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var ProductReview $productReview
         */
        $productReview = $em->getRepository(ProductReview::class)->find($command->id);

        if (!$productReview) {
            throw new NotFoundHttpException('Отзыв не найден');
        }

        $productReview->setAnswer($command->answer);

        $em->persist($productReview);
        $em->flush();
    }
}