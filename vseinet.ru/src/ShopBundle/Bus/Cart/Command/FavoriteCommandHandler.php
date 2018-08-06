<?php 

namespace ShopBundle\Bus\Cart\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use ShopBundle\Bus\Favorite\Command\AddCommand;
use ShopBundle\Bus\Favorite\Command\AddCommandHandler;
use ShopBundle\Entity\Cart;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FavoriteCommandHandler extends MessageHandler
{
    public function handle(FavoriteCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        if ($currentUser) {
            $model = $em->getRepository(Cart::class)->findOneBy(['productId' => $command->id, 'userId' => $currentUser->getId(),]);

            if (!$model) {
                throw new NotFoundHttpException();
            }

            $em->getConnection()->beginTransaction();
            try {
                $addCommand = new AddCommand();
                $addCommand->id = $command->id;
                $addCommandHandler = new AddCommandHandler();
                $addCommandHandler->handle($addCommand);

                $em->remove($model);
                $em->flush();

                $em->getConnection()->commit();
            } catch (\Exception $ex) {
                $em->getConnection()->rollback();

                throw $ex;
            }
        }
    }
}