<?php 

namespace AppBundle\Enum;

class ProductAvailabilityCode
{
    const OUT_OF_STOCK = 'out_of_stock';
    const AWAITING = 'awaiting';
    const ON_DEMAND = 'on_demand';
    const IN_TRANSIT = 'in_transit';
    const AVAILABLE = 'available';
}