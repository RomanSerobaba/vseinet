<?php 

namespace AppBundle\Bus\Order\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use AppBundle\Bus\Exception\ValidationException;
use AppBundle\Entity\ClientOrder;

class GetStatusQueryHandler extends MessageHandler
{
    public function handle(GetStatusQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $order = $em->getRepository(ClientOrder::class)->find($query->number);
        if (!$order instanceof ClientOrder) {
            throw new ValidationException([
                'number' => 'Заказ не найден',
            ]);
        }

        $api = $this->get('api.client');
        try {
            return $api->get('/api/v1/orderItems/?orderIds[]='.$query->number);

        } catch (BadRequestHttpException $e) {
            throw new ValidationException([
                'number' => $e->getMessage(),
            ]);
        }
    }
}