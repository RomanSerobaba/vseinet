<?php 

namespace ShopBundle\Bus\BannerTemplate\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\ResultSetMapping;
use SupplyBundle\Entity\ViewSupplierOrderItem;
use ReservesBundle\Entity\Shipment;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $sql = 'DELETE FROM banner_main_template WHERE id = :template_id';
        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('template_id', $command->id);
        $statement->execute();
    }
}