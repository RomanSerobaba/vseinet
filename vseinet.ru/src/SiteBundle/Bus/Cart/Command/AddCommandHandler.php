<?php 

namespace SiteBundle\Bus\Cart\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProduct;
use SiteBundle\Entity\Cart;

class AddCommandHandler extends MessageHandler
{
    public function handle(AddCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(BaseProduct::class)->find($command->id);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $command->id));
        }

        if ($this->get('user.identity')->isAuthorized()) {
            $user = $this->get('user.identity')->getUser();

            $item = $em->getRepository(Cart::class)->findOneBy([
                'userId' => $user->getId(),
                'baseProductId' => $product->getId(), 
            ]);
            if (!$item instanceof Cart) {
                $item = new Cart();
                $item->setBaseProductId($product->getId());
                $item->setUserId($user->getId());
                $item->setQuantity(0);
            }
            $quantity = $item->getQuantity() + $command->quantity;
            if ($quantity % $product->getMinQuantity()) {
                $quantity = floor($quantity / $product->getMinQuantity()) + $product->getMinQuantity();
            }
            $item->setQuantity($quantity);
            $em->persist($item);
            $em->flush();
        }
        else {
            $cart = $this->get('session')->get('cart', []);
            $item = isset($cart[$product->getId()]) ? $cart[$product->getId()] : ['quantity' => 0];
            $quantity = $item['quantity'] + $command->quantity;
            if ($quantity % $product->getMinQuantity()) {
                $quantity = floor($quantity / $product->getMinQuantity()) + $product->getMinQuantity();
            }
            $cart[$product->getId()]['quantity'] = $quantity;
            $this->get('session')->set('cart', $cart);
        }
    }
}
