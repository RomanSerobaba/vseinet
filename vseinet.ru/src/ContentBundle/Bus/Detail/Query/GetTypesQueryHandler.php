<?php 

namespace ContentBundle\Bus\Detail\Query;

use AppBundle\Bus\Message\MessageHandler;
use ContentBundle\Entity\DetailType;

class GetTypesQueryHandler extends MessageHandler
{
    public function handle(GetTypesQuery $query)
    {
       return $this->getDoctrine()->getManager()->getRepository(DetailType::class)->findBy([], ['code' => 'ASC']);
    }
}