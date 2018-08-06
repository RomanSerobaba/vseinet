<?php 

namespace AppBundle\Enum;

class DocumentTypeCode
{
    const SUPPLIER_RESERVE_ITEM = 'supplier_reserve_item';
    const SUPPLIER_RESERVE = 'supplier_reserve';
    const INVENTORY = 'inventory';
    const SHIPMENT = 'shipment';
    const ORDER_ITEM = 'order_item';
    const EQUIPMENT = 'equipment';
    const GOODS_REQUEST = 'goods_request';
    const GOODS_ISSUE = 'goods_issue';
    const GOODS_DECISION = 'goods_decision';
    const GOODS_PACKAGING = 'goods_packaging';
    const GOODS_MOVEMENT = 'goods_movement';
    const GOODS_RELEASE = 'goods_release';
    const GOODS_REQUEST_ANNUL = 'goods_request_annul';
    const ORDER_ITEM_ANNUL = 'order_item_annul';
    const ORDER_ANNUL = 'order_annul';
    const SUPPLIER_INVOICE = 'supplier_invoice';
    const SUPPLIER_PRODUCT = 'product';
    const SUPPLIER_GOODS_RESERVATION = 'supplier_goods_reservation';
    const ORDER = 'order';
    const AVAILABLE_GOODS_RESERVATION = 'available_goods_reservation';
    const GOODS_ACCEPTANCE = 'goods_acceptance';
    const SUPPLY = 'supply';
    const GOODS_ISSUE_DECISION = 'goods_issue_decision';
    const ORDER_RETURN = 'order_return';
    const DELIVERY = 'delivery';
    const AVAILABLE_RESERVE = 'available_reserve';
    const ORDER_RECEIPT = 'order_receipt';
    const SUPPLIER_RESERVATION = 'supplier_reservation';
}