<?php 

namespace SiteBundle\Bus\Cart\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProduct;
use SiteBundle\Entity\Cart;

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

            $item = $em->getRepository(Cart::class)->findOnBy([
                'userId' => $user->getId(),
                'baseProductId' => $product->getId(), 
            ]);
            if (!$item instanceof Cart) {
                throw new NotFoundHttpException(sprintf('Товара %d нет в корзине', $product->getId()));
            }
            $em->remove($item);
            $em->flush();
        }
        else {
            $cart = $this->get('session')->get('cart', []);
            if (!isset($cart[$product->getId()])) {
                throw new NotFoundHttpException(sprintf('Товара %d нет в корзине', $product->getId()));   
            }
            unset($cart[$product->getId()]);
            $this->get('session')->set('cart', $cart);
        }
    }
}
