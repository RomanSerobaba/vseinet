<?php 

namespace SiteBundle\Bus\Favorite\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProduct;
use SiteBundle\Entity\Favorite;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(BaseProduct::class)->find($command->id);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $command->id));
        }

        if ($this->get('user.identity')->isAuthorized()) {
            $user = $this->get('user.identity')->getUser();

            $favorite = $em->getRepository(Favorite::class)->findOnBy([
                'userId' => $user->getId(),
                'baseProductId' => $product->getId(), 
            ]);
            if (!$favorite instanceof Favorite) {
                throw new NotFoundHttpException(sprintf('Товара с кодом %d нет в избранном', $product->getId()));
            }
            $em->remove($favorite);
        }
        else {
            $favorites = $this->get('session')->get('favorites', []);
            if (!isset($favorites[$product->getId()])) {
                throw new NotFoundHttpException(sprintf('Товара с кодом %d нет в избранном', $product->getId()));
            }
            unset($favorites[$product->getId()]);
            $this->get('session')->set('favorites', $favorites);
        }
    }
}
