<?php 

namespace ShopBundle\Bus\Cart\Command;

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

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
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
            $cart = $this->get('session')->get('cart');
            unset($cart[$command->id]);
            $this->get('session')->set('cart', $cart);
        } else {
            $model = $em->getRepository(Cart::class)->findOneBy(['productId' => $command->id, 'userId' => $currentUser->getId(),]);

            if (!$model) {
                throw new NotFoundHttpException();
            }

            $em->remove($model);
            $em->flush();
        }
    }
}