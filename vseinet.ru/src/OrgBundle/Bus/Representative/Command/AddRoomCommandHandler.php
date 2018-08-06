<?php 

namespace OrgBundle\Bus\Representative\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\ResultSetMapping;
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

class AddRoomCommandHandler extends MessageHandler
{
    public function handle(AddRoomCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $representativeModel = $em->getRepository(Representative::class)->findOneBy(['geo_point_id' => $command->id,]);

        if (!$representativeModel) {
            throw new NotFoundHttpException('Представление не найдено');
        }

        $sql = '
            SELECT
                gr.id,
                gr.name
            FROM
                geo_room gr 
            WHERE
                gr.geo_point_id = :geo_point_id
        ';

        $q = $em->createNativeQuery($sql, new ResultSetMapping());
        $q->setParameter('geo_point_id', $command->id);

        $rooms = $q->getResult('ListAssocHydrator');

        $isFound = false;
        foreach ($rooms as $room) {
            if ($room['name'] === $command->name) {
                $isFound = true;
                break;
            }
        }

        if (!$isFound) {
            $model = new GeoRoom();
            $model->setGeoPointId($command->id);
            $model->setName($command->name);

            $em->persist($model);
            $em->flush();
        } else {
            throw new BadRequestHttpException('Помещение с именем "'.$command->name.'" уже существует у данного представления');
        }
    }
}