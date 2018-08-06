<?php 

namespace ContentBundle\Bus\Supplier\Query;

use AppBundle\Bus\Message\MessageHandler;
use SupplyBundle\Entity\Supplier;

class GetAllQueryHandler extends MessageHandler
{
    public function handle(GetAllQuery $query)
    {
        $items = $this->getDoctrine()->getManager()->getRepository(Supplier::class)->findAll();

        return $items;
    }
}