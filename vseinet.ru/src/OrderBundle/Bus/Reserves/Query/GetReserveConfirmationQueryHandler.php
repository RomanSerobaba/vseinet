<?php

namespace OrderBundle\Bus\Reserves\Query;

use AppBundle\Bus\Message\MessageHandler;
use ServiceBundle\Services\OrderService;

class GetReserveConfirmationQueryHandler extends MessageHandler
{
    public function handle(GetReserveConfirmationQuery $query)
    {
        /**
         * @var OrderService $service
         */
        $service = $this->get('service.order');

        return $this->camelizeKeys($service->reserveItem($query->id, $query->quantities, $query->supplierReserveId, true));
    }
}