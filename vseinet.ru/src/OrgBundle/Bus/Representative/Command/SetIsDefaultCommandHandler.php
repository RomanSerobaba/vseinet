<?php 

namespace OrgBundle\Bus\Representative\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use http\Exception\BadQueryStringException;
use OrgBundle\Entity\GeoPoint;
use OrgBundle\Entity\GeoRoom;
use OrgBundle\Entity\Representative;
use OrgBundle\Entity\RepresentativePhoto;
use OrgBundle\Entity\RepresentativeSchedule;
use ServiceBundle\Components\Image;
use ServiceBundle\Components\Utils;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ThirdPartyBundle\Entity\GeoAddress;

class SetIsDefaultCommandHandler extends MessageHandler
{
    public function handle(SetIsDefaultCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $representativeModel = $em->getRepository(Representative::class)->findOneBy(['geo_point_id' => $command->id,]);

        if (!$representativeModel) {
            throw new NotFoundHttpException('Представление не найдено');
        }

        $geoRoomModel = $em->getRepository(GeoRoom::class)->find($command->geoRoomId);

        if (!$geoRoomModel) {
            throw new NotFoundHttpException('Помещение не найдено');
        }

        $geoRoomModel->setIsDefault($command->isDefault);

        $em->persist($geoRoomModel);
        $em->flush();
    }
}