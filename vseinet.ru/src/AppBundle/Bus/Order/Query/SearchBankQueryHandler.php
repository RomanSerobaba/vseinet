<?php

namespace AppBundle\Bus\Order\Query;

use AppBundle\Bus\Message\MessageHandler;

class SearchBankQueryHandler extends MessageHandler
{
    public function handle(SearchBankQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT
                NEW AppBundle\Bus\Order\Query\DTO\BankFound (
                    b.id,
                    b.name,
                    b.bic
                )
            FROM AppBundle:Bank AS b
            WHERE b.bic LIKE :name
            ORDER BY b.name
        ");
        $q->setParameter('name', $query->q.'%');
        $q->setMaxResults($query->limit);
        $banks = $q->getResult();

        return $banks;
    }
}
