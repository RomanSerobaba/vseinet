<?php 

namespace AppBundle\Bus\Main\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Bus\Exception\ValidationException;
use AppBundle\Enum\BaseProduct;

class GetDataForCheaperRequestQueryHandler extends MessageHandler
{
    public function handle(GetDataFormCheaperRequestQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(BaseProduct::class)->find($query->id);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар %d не найден', $query->id));
        }

        $data = new DTO\DataFormCheaperRequest();

        if (null !== $user = $this->getUser()) {
            $data->fullname = $user->person->getFullname();
        }
    }
}
