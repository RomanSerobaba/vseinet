<?php 

namespace AppBundle\Enum;

class GoodsReserveOperationCode
{
    const ANNUL_RESERVE = 'annul_reserve';
    const INVENTORY = 'inventory';
    const ISSUE = 'issue';
    const SHIP_SUPPLIER_SHIPMENT = 'ship_supplier_shipment';
    const ACCEPT_SUPPLIER_SHIPMENT = 'accept_supplier_shipment';
    const RESERVE = 'reserve';
    const RETURN_FROM_ISSUE = 'return_from_issue';
    const COMPLETE_ISSUE = 'complete_issue';
    const SALE = 'sale';
    const WRITE = 'write';
    const SHIP_INTERNAL_SHIPMENT = 'ship_internal_shipment';
    const ACCEPT_INTERNAL_SHIPMENT = 'accept_internal_shipment';
    const SHIP_DELIVERY_SHIPMENT = 'ship_delivery_shipment';
    const REPORT_DELIVERY_SHIPMENT = 'report_delivery_shipment';
    const PACKAGING = 'packaging';
    const UNPACKAGING = 'unpackaging';
    const MOVEMENT = 'movement';
}