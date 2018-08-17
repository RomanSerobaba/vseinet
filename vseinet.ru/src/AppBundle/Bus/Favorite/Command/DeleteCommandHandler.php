<?php 

namespace AppBundle\Bus\Favorite\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\Favorite;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $baseProduct = $em->getRepository(BaseProduct::class)->find($command->id);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $command->id));
        }

        if (null !== $user) {
            $user = $this->get('user.identity')->getUser();

            $favorite = $em->getRepository(Favorite::class)->findOnBy([
                'userId' => $user->getId(),
                'baseProductId' => $baseProduct->getId(), 
            ]);
            if (!$favorite instanceof Favorite) {
                throw new NotFoundHttpException(sprintf('Товара с кодом %d нет в избранном', $baseProduct->getId()));
            }
            $em->remove($favorite);
        }
        else {
            $favorites = $this->get('session')->get('favorites', []);
            if (!isset($favorites[$baseProduct->getId()])) {
                throw new NotFoundHttpException(sprintf('Товара с кодом %d нет в избранном', $baseProduct->getId()));
            }
            unset($favorites[$baseProduct->getId()]);
            $this->get('session')->set('favorites', $favorites);
        }
    }
}
