<?php 

namespace AppBundle\Bus\Cart\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\Cart;

class SetQuantityCommandHandler extends MessageHandler
{
    public function handle(SetQuantityCommand $command)
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
            if (0 === $command->quantity) {
                $em->remove($item);
            } else {
                $quantity = $command->quantity;
                if ($quantity % $baseProduct->getMinQuantity()) {
                    $quantity = ceil($quantity / $baseProduct->getMinQuantity()) * $baseProduct->getMinQuantity();
                }
                $item->setQuantity($quantity);
                $em->persist($item);
            }
            $em->flush();
        }
        else {
            $cart = $this->get('session')->get('cart', []);
            if (!isset($cart[$baseProduct->getId()])) {
                throw new NotFoundHttpException(sprintf('Товара %d нет в корзине', $baseProduct->getId()));   
            }
            if (0 === $command->quantity) {
                unset($cart[$baseProduct->getId()]);
            } else {
                $quantity = $command->quantity;
                if ($quantity % $baseProduct->getMinQuantity()) {
                    $quantity = ceil($quantity / $baseProduct->getMinQuantity()) * $baseProduct->getMinQuantity();
                }
                $cart[$baseProduct->getId()]['quantity'] = $quantity;
            }
            $this->get('session')->set('cart', $cart);
        }
    }
}
