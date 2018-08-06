<?php 

namespace AppBundle\Enum;

class OperationTypeCode
{
    const ORDER_CREATION = 'order_creation';
    const GOODS_REQUEST_ANNUL = 'goods_request_annul';
    const ORDER_ITEM_ANNUL = 'order_item_annul';
    const ORDER_ANNUL = 'order_annul';
    const SUPPLIER_GOODS_RESERVATION = 'supplier_goods_reservation';
    const SUPPLIER_INVOICE_CREATION = 'supplier_invoice_creation';
    const SUPPLIER_INVOICE_CLOSING = 'supplier_invoice_closing';
    const SUPPLIER_INVOICE_DELETING = 'supplier_invoice_deleting';
    const SUPPLIER_INVOICE_CHANGING = 'supplier_invoice_changing';
    const SUPPLIER_INVOICE_ADDING = 'supplier_invoice_adding';
    const SUPPLIER_RESERVE_CLOSING = 'supplier_reserve_closing';
    const SUPPLIER_RESERVE_CHANGE = 'supplier_reserve_change';
    const AVAILABLE_GOODS_RESERVATION = 'available_goods_reservation';
    const GOODS_REQUEST_CREATION = 'goods_request_creation';
    const SALE = 'sale';
    const PACKAGING = 'packaging';
    const INVENTORY = 'inventory';
    const RESUPPLY_ORDER_CREATING = 'resupply_order_creating';
    const INNER_TRANSITION = 'inner_transition';
    const GOODS_ISSUE_DECISION = 'goods_issue_decision';
    const DELIVERY_REPORTING = 'delivery_reporting';
    const GOODS_RELEASE = 'goods_release';
    const ORDER_RETURNING = 'order_returning';
    const ORDER_RETURN = 'order_return';
    const DELIVERY = 'delivery';
    const COURIER_DELIVERY = 'courier_delivery';
    const SUPPLY_ACCEPTING = 'supply_accepting';
    const GOODS_ACCEPTANCE = 'goods_acceptance';
    const GOODS_PACKAGING = 'goods_packaging';
    const GOODS_ISSUE_CREATION = 'goods_issue_creation';
    const SUPPLIER_RESERVATION = 'supplier_reservation';
    const AVAILABLE_RESERVATION = 'available_reservation';
    const SUPPLIER_INVOICE_REGISTRATION = 'supplier_invoice_registration';
    const ORDER_RECEIPT = 'order_receipt';
    const INVENTORY_COMPLETING = 'inventory_completing';
    const GOODS_MOVEMENT = 'goods_movement';
    const SUPPLY_FORMING = 'supply_forming';
    const SUPPLY_ITEM_DELETING = 'supply_item_deleting';
    const SUPPLY_ITEM_CHANGE = 'supply_item_change';
    const SUPPLY_ITEM_ADDING = 'supply_item_adding';
    const SUPPLY_SHIPPING = 'supply_shipping';
}