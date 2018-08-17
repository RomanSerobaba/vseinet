<?php 

namespace AppBundle\Bus\Vacancy\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\Vacancy;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        return $this->getDoctrine()->getManager()->getRepository(Vacancy::class)->findBy(['isActive' => true], ['position' => 'ASC']);
    }
}
