<?php
namespace FinanseBundle\Bus\ExpenseSimpleDoc\Query;

use AppBundle\Bus\Message\MessageHandler;

class StatusesQueryHandler extends MessageHandler
{
    public function handle(StatusesQuery $query)
    {
        return $this->get('document.status')
                        ->listAll(\FinanseBundle\Entity\ExpenseSimpleDoc::class, $query->onlyActive);
    }

}
