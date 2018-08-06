<?php 

namespace FinanseBundle\Bus\BankOperationDoc\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\ORM\Query\DTORSM;
use ReservesBundle\AddOn\TempStorage;

class ImportResultQueryHandler extends MessageHandler
{
    public function handle(ImportResultQuery $query)
    {

        $tempStorage = new TempStorage();
        $result = json_decode($tempStorage->getData($query->uuid), true);

        return $result;
        
    }

}
