<?php 

namespace AppBundle\Bus\Favorite\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\Favorite;

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
            $favorite = $em->getRepository(Favorite::class)->findOnBy([
                'userId' => $user->getId(),
                'baseProductId' => $baseProduct->getId(), 
            ]);
            if (!$favorite instanceof Favorite) {
                $favorite = new Favorite();
                $favorite->setBaseProductId($baseProduct->getId());
                $favorite->setUserId($user->getId());
                $em->persist($item);
            }
        }
        else {
            $favorites = $this->get('session')->get('favorites', []);
            if (!isset($favorites[$baseProduct->getId()])) {
                $favorites[$baseProduct->getId()] = $baseProduct->getId();
            }
            $this->get('session')->set('favorites', $favorites);
        }
    }
}
