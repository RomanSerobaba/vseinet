<?php 

namespace AppBundle\Bus\Resource\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\Resource;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        return $this->getDoctrine()->getManager()->getRepository(Resource::class)->findBy([], ['name' => 'ASC']);
    }
}