<?php 

namespace PricingBundle\Bus\Competitors\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\CompetitorTypeCode;
use PricingBundle\Entity\Competitor;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ThirdPartyBundle\Entity\GeoAddress;

class EditCommandHandler extends MessageHandler
{
    public function handle(EditCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $em->getConnection()->beginTransaction();
        try {
            $competitor = $em->getRepository(Competitor::class)->find($command->id);

            if (!$competitor) {
                throw new NotFoundHttpException('Конкурент не найден');
            }

            if ($competitor->getChannel() === CompetitorTypeCode::SITE) {
                if (!empty($command->link)) {
                    if (!filter_var($command->link, FILTER_VALIDATE_URL)) {
                        throw new \Exception('Неверный формат ссылки');
                    }

                    // Обновляем конкурента
                    $competitor->setLink($command->link);
                    $competitor->setGeoCityId(!empty($command->geoCityId) ?: null);
                    $competitor->setIsUtf($command->isUtfCoding);

                    $em->persist($competitor);
                } else {
                    throw new \Exception('Ссылка отсутствует');
                }
            } elseif ($competitor->getChannel() === CompetitorTypeCode::PRICELIST) {
                if (empty($command->supplierId)) {
                    throw new BadRequestHttpException('Отсутствует поставщик');
                }

                // Обновляем конкурента
                $competitor->setSupplierId($command->supplierId);
                $competitor->setGeoCityId(!empty($command->geoCityId) ?: null);
                $em->persist($competitor);
            } elseif ($competitor->getChannel() === CompetitorTypeCode::RETAIL) {
                if (empty($command->geoStreetId)) {
                    throw new BadRequestHttpException('Отсутствуют данные адреса: улица');
                }
                if (empty($command->house)) {
                    throw new BadRequestHttpException('Отсутствуют данные адреса: дом');
                }
                if (empty($command->building)) {
                    throw new BadRequestHttpException('Отсутствуют данные адреса: строение');
                }
                if (empty($command->floor)) {
                    throw new BadRequestHttpException('Отсутствуют данные адреса: этаж');
                }

                if (!empty($competitor->getGeoAddressId())) {
                    $geoAddress = $em->getRepository(GeoAddress::class)->find($competitor->getGeoAddressId());

                    if (!$geoAddress) {
                        throw new NotFoundHttpException('Адрес, на который ссылается конкурент, не существует: '.$competitor->getGeoAddressId());
                    }
                } else {
                    $geoAddress = new GeoAddress();
                }

                $geoAddress->setGeoStreetId($command->geoStreetId);
                $geoAddress->setHouse($command->house);
                $geoAddress->setBuilding($command->building);
                $geoAddress->setFloor($command->floor);

                $em->persist($geoAddress);

                // Обновляем конкурента
                $competitor->setGeoAddressId($geoAddress->getId());
                $competitor->setGeoCityId(!empty($command->geoCityId) ?: null);
                $em->persist($competitor);
            }

            $em->flush();
            $em->getConnection()->commit();
        } catch (\Exception $ex) {
            $em->getConnection()->rollback();

            throw $ex;
        }
    }
}