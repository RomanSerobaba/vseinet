<?php 

namespace ShopBundle\Bus\Cart\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\ResultSetMapping;
use ShopBundle\Entity\BannerMainData;
use ShopBundle\Entity\BannerMainTemplate;
use ShopBundle\Entity\Favorite;
use SupplyBundle\Entity\ViewSupplierOrderItem;
use ReservesBundle\Entity\Shipment;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClearCommandHandler extends MessageHandler
{
    public function handle(ClearCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        if (!$currentUser) {
            $this->get('session')->remove('cart');
        } else {
            $sql = 'DELETE FROM cart WHERE user_id = :id';
            $statement = $em->getConnection()->prepare($sql);
            $statement->bindValue('id', $currentUser->getId());
            $statement->execute();
        }
    }
}