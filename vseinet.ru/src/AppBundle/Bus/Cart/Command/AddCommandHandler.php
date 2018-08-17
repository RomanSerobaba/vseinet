<?php 

namespace AppBundle\Bus\Cart\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\Cart;

class AddCommandHandler extends MessageHandler
{
    public function handle(AddCommand $command)
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
                $item = new Cart();
                $item->setBaseProductId($baseProduct->getId());
                $item->setUserId($user->getId());
                $item->setQuantity(0);
            }
            $quantity = $item->getQuantity() + $command->quantity;
            if ($quantity % $baseProduct->getMinQuantity()) {
                $quantity = floor($quantity / $baseProduct->getMinQuantity()) + $baseProduct->getMinQuantity();
            }
            $item->setQuantity($quantity);
            $em->persist($item);
            $em->flush();
        }
        else {
            $cart = $this->get('session')->get('cart', []);
            $item = isset($cart[$baseProduct->getId()]) ? $cart[$baseProduct->getId()] : ['quantity' => 0];
            $quantity = $item['quantity'] + $command->quantity;
            if ($quantity % $baseProduct->getMinQuantity()) {
                $quantity = floor($quantity / $baseProduct->getMinQuantity()) + $baseProduct->getMinQuantity();
            }
            $cart[$baseProduct->getId()]['quantity'] = $quantity;
            $this->get('session')->set('cart', $cart);
        }
    }
}
