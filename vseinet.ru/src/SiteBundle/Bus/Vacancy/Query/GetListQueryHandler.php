<?php 

namespace SiteBundle\Bus\Vacancy\Query;

use AppBundle\Bus\Message\MessageHandler;
use SiteBundle\Entity\Vacancy;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        return $this->getDoctrine()->getManager()->getRepository(Vacancy::class)->findBy(['isActive' => true], ['position' => 'ASC']);
    }
}
