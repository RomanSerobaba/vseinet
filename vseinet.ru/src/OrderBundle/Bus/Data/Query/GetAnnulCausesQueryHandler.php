<?php 

namespace OrderBundle\Bus\Data\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetAnnulCausesQueryHandler extends MessageHandler
{
    /**
     * @param GetAnnulCausesQuery $query
     *
     * @return array
     */
    public function handle(GetAnnulCausesQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery('
            SELECT
                NEW OrderBundle\Bus\Data\Query\DTO\AnnulCause (oac.code, oac.name)
            FROM OrderBundle:OrderAnnulCause oac
            ORDER BY oac.name
        ');

        return $q->getResult();
    }
}