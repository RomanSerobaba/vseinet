<?php 

namespace AppBundle\Bus\ResourceGroup\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\ResourceGroup;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        return $this->getDoctrine()->getManager()->getRepository(ResourceGroup::class)->findBy([], ['name' => 'ASC']);
    }
}