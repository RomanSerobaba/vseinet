<?php 

namespace DeliveryBundle\Bus\Delivery\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DeliveryBundle\Entity\DeliveryDoc;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\Id\SequenceGenerator;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        if ('transport_company' == $command->type && !$command->transportCompanyId) {
            throw new BadRequestHttpException('Необходимо указать грузоперевозчика');
        }

        $delivery = new DeliveryDoc();
        $delivery->setDate(new \DateTime($command->date));
        $delivery->setType($command->type);
        $delivery->setStatusCode('new');
        $delivery->setCreatedBy($this->get('user.identity')->getUser()->getid());
        $delivery->setGeoPointId($command->pointId);
        $delivery->setTransportCompanyId($command->transportCompanyId);
        $sequence = 'delivery_doc_number_seq';
        $sequenceGenerator = new SequenceGenerator($sequence, 1);
        $number = $sequenceGenerator->generate($em, $delivery);
        $delivery->setNumber($number);
        $delivery->setTitle('Доставка №' . $number . ' от ' . $command->date);
        $em->persist($delivery);
        $this->get('uuid.manager')->saveId($command->uuid, $number);
    }
}