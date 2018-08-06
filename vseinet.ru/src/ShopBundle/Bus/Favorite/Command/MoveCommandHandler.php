<?php 

namespace ShopBundle\Bus\Favorite\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\ResultSetMapping;
use ShopBundle\Entity\BannerMainData;
use ShopBundle\Entity\BannerMainTemplate;
use ShopBundle\Entity\Cart;
use ShopBundle\Entity\Favorite;
use SupplyBundle\Entity\ViewSupplierOrderItem;
use ReservesBundle\Entity\Shipment;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MoveCommandHandler extends MessageHandler
{
    public function handle(MoveCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        $model = $em->getRepository(Favorite::class)->findOneBy(['productId' => $command->id, 'createdBy' => $currentUser->getId(),]);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        $em->getConnection()->beginTransaction();
        try {
            $cart = new Cart();
            $cart->setUserId($currentUser->getId());
            $cart->setQuantity(1);
            $cart->setProductId($command->id);

            $em->persist($cart);

            $em->remove($model);
            $em->flush();

            $em->getConnection()->commit();
        } catch (\Exception $ex) {
            $em->getConnection()->rollback();

            throw $ex;
        }
    }
}