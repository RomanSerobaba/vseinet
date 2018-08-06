<?php 

namespace AppBundle\Enum;

class OrderItemStatusCode
{
    const LACK = 'lack';
    const PREPAYABLE = 'prepayable';
    const CALLABLE = 'callable';
    const SHIPPING = 'shipping';
    const TRANSIT = 'transit';
    const STATIONED = 'stationed';
    const ARRIVED = 'arrived';
    const ANNULLED = 'annulled';
    const CANCELED = 'canceled';
    const TRANSPORT = 'transport';
    const RELEASABLE = 'releasable';
    const COMPLETED = 'completed';
    const COURIER = 'courier';
    const ISSUED = 'issued';
    const REFUNDED   = 'refunded';
    const CREATED = 'created';
    const POST = 'post';
}