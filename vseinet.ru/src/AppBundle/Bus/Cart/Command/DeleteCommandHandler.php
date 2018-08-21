<?php 

namespace AppBundle\Bus\Cart\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\Cart;

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
            $item = $em->getRepository(Cart::class)->findOneBy([
                'userId' => $user->getId(),
                'baseProductId' => $baseProduct->getId(), 
            ]);
            if (!$item instanceof Cart) {
                throw new NotFoundHttpException(sprintf('Товара %d нет в корзине', $baseProduct->getId()));
            }
            $em->remove($item);
            $em->flush();
        }
        else {
            $cart = $this->get('session')->get('cart', []);
            if (!isset($cart[$baseProduct->getId()])) {
                throw new NotFoundHttpException(sprintf('Товара %d нет в корзине', $baseProduct->getId()));   
            }
            unset($cart[$baseProduct->getId()]);
            $this->get('session')->set('cart', $cart);
        }
    }
}
