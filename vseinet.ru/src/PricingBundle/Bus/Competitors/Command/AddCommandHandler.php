<?php 

namespace PricingBundle\Bus\Competitors\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\CompetitorTypeCode;
use PricingBundle\Entity\Competitor;
use ServiceBundle\Components\Utils;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ThirdPartyBundle\Entity\GeoAddress;

class AddCommandHandler extends MessageHandler
{
    public function handle(AddCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        if (!in_array($command->typeCode, [CompetitorTypeCode::SITE, CompetitorTypeCode::RETAIL, CompetitorTypeCode::PRICELIST,])) {
            throw new BadRequestHttpException('Неверный тип конкурента');
        }

        $em->getConnection()->beginTransaction();
        try {
            $competitor = new Competitor();
            $competitor->setName($command->name);
            $competitor->setChannel($command->typeCode);
            $competitor->setIsActive(true);
            $competitor->setGeoCityId(!empty($command->geoCityId) ?: null);

            $alias = Utils::translitIt($command->name, true);

            if ($command->typeCode === CompetitorTypeCode::SITE) {
                if (!empty($command->link)) {
                    if (!filter_var($command->link, FILTER_VALIDATE_URL)) {
                        throw new \Exception('Неверный формат ссылки');
                    }

                    // Добавляем конкурента
                    $competitor->setLink($command->link);
                    $competitor->setAlias($alias);
                    $competitor->setIsUtf($command->isUtfCoding);

                    $em->persist($competitor);
                } else {
                    throw new \Exception('Ссылка отсутствует');
                }
            } elseif ($command->typeCode === CompetitorTypeCode::PRICELIST) {
                if (empty($command->supplierId)) {
                    throw new BadRequestHttpException('Отсутствует поставщик');
                }

                // Добавляем конкурента
                $competitor->setSupplierId($command->supplierId);

                $em->persist($competitor);
            } elseif ($command->typeCode === CompetitorTypeCode::RETAIL) {
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

                // добавляем в базу новый адрес
                $geoAddress = new GeoAddress();
                $geoAddress->setGeoStreetId($command->geoStreetId);
                $geoAddress->setHouse($command->house);
                $geoAddress->setBuilding($command->building);
                $geoAddress->setFloor($command->floor);

                $em->persist($geoAddress);

                // Добавляем конкурента 
                $competitor->setGeoAddressId($geoAddress->getId());
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