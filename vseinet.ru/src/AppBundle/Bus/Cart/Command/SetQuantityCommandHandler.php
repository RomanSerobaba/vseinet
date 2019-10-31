<?php

namespace AppBundle\Bus\Cart\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\Cart;
use AppBundle\Entity\Product;
use AppBundle\Enum\ProductAvailabilityCode;

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

        $products = $em->getRepository(Product::class)->findBy(['baseProductId' => $command->id, 'geoCityId' => [0, $this->getGeoCity()->getId()]], ['geoCityId' => 'DESC']);
        $product = reset($products);
        $minQuantity = ProductAvailabilityCode::AVAILABLE === $product->getProductAvailabilityCode() ? 1 : $baseProduct->getMinQuantity();

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
                if ($quantity % $minQuantity) {
                    $quantity = ceil($quantity / $minQuantity) * $minQuantity;
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
                if ($quantity % $minQuantity) {
                    $quantity = ceil($quantity / $minQuantity) * $minQuantity;
                }
                $cart[$baseProduct->getId()]['quantity'] = $quantity;
            }
            $this->get('session')->set('cart', $cart);
        }
    }
}
