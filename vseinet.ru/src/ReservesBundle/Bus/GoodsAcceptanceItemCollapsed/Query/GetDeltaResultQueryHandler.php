<?php 
namespace ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\Query;

use AppBundle\Bus\Message\MessageHandler;
use ReservesBundle\AddOn\TempStorage;

class GetDeltaResultQueryHandler extends MessageHandler
{
    public function handle(GetDeltaResultQuery $query)
    {

        $tempStorage = new TempStorage();
        $result = json_decode($tempStorage->getData($query->uuid), true);

        return $result;

    }
    
}