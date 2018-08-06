<?php 

namespace OrgBundle\Bus\Representative\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use http\Exception\BadQueryStringException;
use OrgBundle\Entity\GeoPoint;
use OrgBundle\Entity\Representative;
use OrgBundle\Entity\RepresentativePhoto;
use OrgBundle\Entity\RepresentativeSchedule;
use ServiceBundle\Components\Image;
use ServiceBundle\Components\Utils;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ThirdPartyBundle\Entity\GeoAddress;

class SaveRepresentativeCommandHandler extends MessageHandler
{
    public function handle(SaveRepresentativeCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        if (empty($command->id)) { // new
            if (empty($command->geoPointId)) {
                throw new BadRequestHttpException('Не указана точка');
            }

            $representativeModel = new Representative();
            $representativeModel->setGeoPointId($command->geoPointId);
            $geoPointId = $command->geoPointId;
        } else {
            $representativeModel = $em->getRepository(Representative::class)->findOneBy(['geo_point_id' => $command->id,]);

            if (!$representativeModel) {
                throw new NotFoundHttpException('Представление не найдено');
            }
            $geoPointId = $command->id;
        }

        $geoPointModel = $em->getRepository(GeoPoint::class)->find($geoPointId);
        if (!$geoPointModel) {
            throw new NotFoundHttpException('Точка не найдена');
        }

        if (!empty($command->coordinates)) {
            $c = explode(',', $command->coordinates);
            if (count($c) != 2 or ((float)$c[0] != $c[0]) or ((float)$c[1] != $c[1])) {
                throw new BadRequestHttpException('Неверный формат. Используйте сервис определения координат');
            }
        }
        if (count($command->schedule) != 14) {
            throw new BadRequestHttpException('Заполните расписание');
        }

        // Save model
        $representativeModel->setHasWarehouse($command->hasWarehouse);
        $representativeModel->setHasRetail($command->hasRetail);
        $representativeModel->setHasOrderIssueing($command->hasOrderIssueing);
        $representativeModel->setHasDelivery($command->hasDelivery);
        $representativeModel->setHasRising($command->hasRising);
        $representativeModel->setIsActive($command->isActive);
        $representativeModel->setIsCentral($command->isCentral);
        $representativeModel->setHasTransit($command->hasTransit);
        $representativeModel->setType($command->type);
        $representativeModel->setIp($command->ip);
        $representativeModel->setDeliveryTax($command->deliveryTax);

        $em->persist($representativeModel);

        if (!$representativeModel->getGeoPointId()) {
            throw new BadRequestHttpException('Ошибка сохранения представительства');
        }

        // Geo point
        $geoPointModel->setName($command->geoPointName);
        $geoPointModel->setCode($command->geoPointCode);


        // Geo address
        if(empty($geoPointModel->getGeoAddressId())) {
            $geoAddressModel = new GeoAddress();
        } else {
            $geoAddressModel = $em->getRepository(GeoAddress::class)->find($geoPointModel->getGeoAddressId());
        }

        $geoAddressModel->setCoordinates($command->coordinates);
        $geoAddressModel->setComment($command->address);

        $em->persist($geoAddressModel);

        if(empty($geoPointModel->getGeoAddressId())) {
            $geoPointModel->setGeoAddressId($geoAddressModel->getId());
        }

        $em->persist($geoPointModel);

        //Photos
        $imageComponent = new Image();
        if ($command->photo instanceof UploadedFile) {
            $filename = $representativeModel->getGeoPointId().'_p.'.$command->photo->guessClientExtension();
            $fullPath = $this->getParameter('project.web.contacts.path') . DIRECTORY_SEPARATOR . $filename;

            if (false === ($contentData = @file_get_contents($command->photo->getRealPath()))) {
                throw new BadRequestHttpException('Read photo problem');
            }

            try {
                $imageComponent->resize($contentData, $fullPath, Image::THUMB_WIDTH, Image::THUMB_HEIGHT);
            } catch (\Exception $exception) {
                throw $exception;
            }
        }
        for ($i = 1; $i <= 4; $i++) {
            $photo = !empty($command->{"photo$i"}) ? $command->{"photo$i"} : null;
            if ($photo instanceof UploadedFile) {
                $filename = Utils::translitIt($command->geoPointName).$i.'.'.$photo->guessClientExtension();
                $fullPath = $this->getParameter('project.web.contacts.path') . DIRECTORY_SEPARATOR . $filename;

                if (false === ($contentData = @file_get_contents($photo->getRealPath()))) {
                    throw new BadRequestHttpException('Read photo'.$i.' problem');
                }

                try {
                    $im = $imageComponent->resize($contentData, $fullPath, Image::REPRESENTATIVE_WIDTH, Image::REPRESENTATIVE_HEIGHT);
                    if ($im instanceof \Imagick) {
                        $photoModel = $em->getRepository(RepresentativePhoto::class)->findOneBy(['representative_id' => $representativeModel->getGeoPointId(), 'sortOrder' => $i,]);
                        if (!$photoModel) {
                            $photoModel = new RepresentativePhoto();
                            $photoModel->setRepresenativeId($representativeModel->getGeoPointId());
                            $photoModel->setSortOrder($i);
                        }
                        $photoModel->setTitle(Utils::translitIt($command->geoPointName));
                        $photoModel->setUrl(RepresentativePhoto::WEB_DIR_URL . DIRECTORY_SEPARATOR . $filename);

                        $em->persist($photoModel);
                    }
                } catch (\Exception $exception) {
                    throw $exception;
                }

                @unlink($photo->getRealPath());
            }
        }

        // Schedule
        $scheduleModel = $em->getRepository(RepresentativeSchedule::class)->find($representativeModel->getGeoPointId());
        if (!$scheduleModel) {
            $scheduleModel = new RepresentativeSchedule();
            $scheduleModel->setRepresentativeId($representativeModel->getGeoPointId());
            $scheduleModel->setCreatedAt(new \DateTime());
            $scheduleModel->setCreatedBy($currentUser->getId());
        }
        foreach ($command->schedule as $field => $time) {
            if (empty($time)) {
                $scheduleModel->$field = null;
            } elseif ($scheduleModel->$field != strtotime($time, 0)) {
                $scheduleModel->$field = strtotime($time, 0);
            }
        }

        $em->persist($scheduleModel);

        $em->flush();
    }
}