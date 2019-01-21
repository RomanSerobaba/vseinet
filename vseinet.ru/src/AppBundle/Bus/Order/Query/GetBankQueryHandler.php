<?php

namespace AppBundle\Bus\Order\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetBankQueryHandler extends MessageHandler
{
    public function handle(GetBankQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT
                NEW AppBundle\Bus\Order\Query\DTO\Bank (
                    b.id,
                    b.name
                )
            FROM AppBundle:Bank AS b
            WHERE b.bic = :bic
        ");
        $q->setParameter('bic', $query->bic);
        $bank = $q->getOneOrNullResult();

        return $bank;
    }
}
