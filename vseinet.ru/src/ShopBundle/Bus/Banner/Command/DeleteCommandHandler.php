<?php 

namespace ShopBundle\Bus\Banner\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\ResultSetMapping;
use ShopBundle\Entity\BannerMainData;
use ShopBundle\Entity\BannerMainTemplate;
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

        $model = $em->getRepository(BannerMainData::class)->find($command->id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        $em->remove($model);
        $em->flush();

        $sql = 'DELETE FROM banner_main_product_data WHERE banner_id = :id';
        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('id', $command->id);
        $statement->execute();
    }
}