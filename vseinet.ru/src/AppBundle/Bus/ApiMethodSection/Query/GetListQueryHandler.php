<?php 

namespace AppBundle\Bus\ApiMethodSection\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\ApiMethodSection;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        return $this->getDoctrine()->getManager()->getRepository(ApiMethodSection::class)->findBy([], ['name' => 'ASC']);
    }
}