<?php

namespace AppBundle\Bus\Order\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Bus\Order\Command\Schema\Address;
use AppBundle\Bus\Order\Command\Schema\OrganizationDetails;
use AppBundle\Entity\CompanyToFinancialCounteragent;
use AppBundle\Entity\FinancialCounteragent;
use AppBundle\Entity\Counteragent;
use AppBundle\Entity\WholesaleContract;
use AppBundle\Enum\ContactTypeCode;
use AppBundle\Enum\DeliveryTypeCode;
use AppBundle\Enum\OrderType;
use AppBundle\Enum\OrderTypeCode;
use AppBundle\Enum\PaymentTypeCode;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        $cart = $this->get('query_bus')->handle(new \AppBundle\Bus\Cart\Query\GetQuery(['discountCode' => $this->get('session')->get('discountCode')]));
        $items = [];

        foreach ($cart->products as $product) {
            $items[] = ['baseProductId' => $product->id, 'quantity' => $product->quantity];
        }

        $api = $this->getUserIsEmployee() ? $this->get('user.api.client') : $this->get('site.api.client');

        switch ($command->typeCode) {
            case OrderType::CONSUMABLES:
                $typeCode = OrderTypeCode::CONSUMABLES;
                break;
            case OrderType::EQUIPMENT:
                $typeCode = OrderTypeCode::EQUIPMENT;
                break;
            case OrderType::COMPANY:
            case OrderType::LEGAL:
                $typeCode = OrderTypeCode::LEGAL;
                break;
            case OrderType::NATURAL:
                $typeCode = OrderTypeCode::SITE;
                break;
            case OrderType::RESUPPLY:
                $typeCode = OrderTypeCode::RESUPPLY;
                break;
            case OrderType::RETAIL:
                $typeCode = OrderTypeCode::SHOP;
                break;
        }

        if (empty($typeCode)) {
            throw new BadRequestHttpException('Указан не существующий тип заказа');
        }

        if (OrderType::COMPANY === $command->typeCode) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();

            $clientDTO = new \AppBundle\Bus\Order\Command\Schema\Client();
            $clientDTO->userId = $user->getId();
            $clientDTO->fullname = $user->person->getFullname();
            $q = $em->createQuery('
                SELECT
                    c,
                    CASE WHEN c.isMain = true THEN 1 ELSE 2 END AS HIDDEN ORD1,
                    CASE WHEN c.contactTypeCode = :mobile THEN 1 ELSE 2 END AS HIDDEN ORD2
                FROM AppBundle:Contact AS c
                WHERE c.personId = :personId AND c.contactTypeCode IN (:mobile, :phone)
                ORDER BY ORD1 ASC, ORD2 ASC
            ');
            $q->setParameter('personId', $user->getPersonId());
            $q->setParameter('mobile', ContactTypeCode::MOBILE);
            $q->setParameter('phone', ContactTypeCode::PHONE);
            $phoneList = $q->getResult();

            if (!empty($phoneList[0])) {
                if (ContactTypeCode::MOBILE == $phoneList[0]->getContactTypeCode()) {
                    $clientDTO->phone = $phoneList[0]->getValue();

                    if (!empty($phoneList[1])) {
                        $clientDTO->additionalPhone = $phoneList[1]->getValue();
                    }
                } else {
                    $clientDTO->additionalPhone = $phoneList[0]->getValue();
                }
            }

            $q = $em->createQuery('
                SELECT
                    c,
                    CASE WHEN c.isMain = true THEN 1 ELSE 2 END AS HIDDEN ORD
                FROM AppBundle:Contact AS c
                WHERE c.personId = :personId AND c.contactTypeCode IN (:email)
                ORDER BY ORD ASC
            ');
            $q->setParameter('personId', $user->getPersonId());
            $q->setParameter('email', ContactTypeCode::EMAIL);
            $email = $q->getOneOrNullResult();

            if (!empty($email)) {
                $clientDTO->email = $email->getValue();
            }

            $franchiserFinancialCounteragents = $em->getRepository(CompanyToFinancialCounteragent::class)->findBy(['companyId' => $this->get('representative.identity')->getEmployeeRepresentative()->getCompanyId()]);
            $franchiserFinancialCounteragent = null;

            if (!empty($franchiserFinancialCounteragents)) {
                foreach ($franchiserFinancialCounteragents as $franchiserFinancialCounteragent) {
                    $contract = $em->getRepository(WholesaleContract::class)->findOneBy(['financialCounteragentId' => $franchiserFinancialCounteragent->getFinancialCounteragentId(), 'terminatedAt' => null]);

                    if ($contract) {
                        break;
                    }
                }
            }

            if (empty($contract)) {
                throw new BadRequestHttpException('Не найдено активных оптовых договоров по вашей организации');
            }

            $franchiserFinancialCounteragent = $em->getRepository(FinancialCounteragent::class)->find($franchiserFinancialCounteragent->getFinancialCounteragentId());
            $organizationDetails = new OrganizationDetails();
            $counteragent = $em->getRepository(Counteragent::class)->find($franchiserFinancialCounteragent->getCounteragentId());
            $organizationDetails->tin = $counteragent->getTin();
            $organizationDetails->name = $counteragent->getName();

            $params = [
                'geoPointId' => $this->getParameter('default.point.id'),
                'typeCode' => OrderTypeCode::LEGAL,
                'paymentTypeCode' => PaymentTypeCode::CASH,
                'deliveryTypeCode' => DeliveryTypeCode::EX_WORKS,
                'isCallNeeded' => false,
                'address' => new Address(),
                'client' => $clientDTO,
                'organizationDetails' => $organizationDetails,
                'items' => $items,
            ];
        } else {
            $params = [
                'typeCode' => $typeCode,
                'client' => $command->client,
                'address' => $command->address,
                'passport' => $command->passport,
                'organizationDetails' => $command->organizationDetails,
                'geoCityId' => $command->geoCityId,
                'geoPointId' => $command->geoPointId,
                'withVat' => $command->withVat,
                'paymentTypeCode' => $command->paymentTypeCode,
                'creditDownPayment' => $command->creditDownPayment*100,
                'deliveryTypeCode' => $command->deliveryTypeCode,
                'needLifting' => $command->needLifting,
                'transportCompanyId' => $command->transportCompanyId,
                'isNotificationNeeded' => $this->getUserIsEmployee() ? $command->isNotificationNeeded : true,
                'isMarketingSubscribed' => $command->isMarketingSubscribed,
                'isCallNeeded' => $this->getUserIsEmployee() ? false : $command->isCallNeeded,
                'callNeedComment' => $command->callNeedComment,
                'comment' => $command->comment,
                'discountCode' => $cart->discountCode,
                'items' => $items,
            ];
        }

        try {
            $result = $api->post('/api/v1/orders/', [], $params);
            $command->id = $result['id'];
        } catch (BadRequestHttpException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}
