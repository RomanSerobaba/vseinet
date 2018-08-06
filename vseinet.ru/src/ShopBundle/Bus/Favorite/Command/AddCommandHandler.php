<?php 

namespace ShopBundle\Bus\Favorite\Command;

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

class AddCommandHandler extends MessageHandler
{
    public function handle(AddCommand $command)
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
            $model = new Favorite();
            $model->setProductId($command->id);
            $model->setCreatedAt(new \DateTime());
            $model->setCreatedBy($currentUser->getId());

            $em->persist($model);
            $em->flush();
        }
    }
}