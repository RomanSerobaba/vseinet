<?php

namespace SupplyBundle\Tests\Controller;

use SupplyBundle\Component\OrderComponent;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use SupplyBundle\Bus\Data\Query;
use SupplyBundle\Bus\Data\Query\DTO\SupplyItemsForShippingOrders;
use SupplyBundle\Bus\Data\Query\DTO\SupplyItemsForShippingProducts;
use SupplyBundle\Bus\Data\Query\DTO\SupplierWithInvoices;

class DataControllerTest extends KernelTestCase
{
    public function testGetListAction()
    {
        $id = 35660;

        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetSupplierWithInvoicesQuery(['state' => OrderComponent::STATE_FORMING,]);
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertTrue($item instanceof SupplierWithInvoices);

            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('date', $item);
            $this->assertObjectHasAttribute('point', $item);
            $this->assertObjectHasAttribute('sum', $item);
            $this->assertObjectHasAttribute('quantity', $item);
            $this->assertObjectHasAttribute('supplierCounteragentId', $item);
            $this->assertObjectHasAttribute('ourCounteragent', $item);
            $this->assertObjectHasAttribute('supplierInvoiceNumber', $item);
            $this->assertObjectHasAttribute('creator', $item);
            $this->assertObjectHasAttribute('state', $item);

            $this->assertEquals($item->state, OrderComponent::STATE_FORMING);
        }

        /////////////////////////////////////////////////////////

        $query = new Query\GetSupplierWithInvoicesQuery(['state' => OrderComponent::STATE_TRANSIT,]);
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertTrue($item instanceof SupplierWithInvoices);

            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('date', $item);
            $this->assertObjectHasAttribute('point', $item);
            $this->assertObjectHasAttribute('sum', $item);
            $this->assertObjectHasAttribute('quantity', $item);
            $this->assertObjectHasAttribute('supplierCounteragentId', $item);
            $this->assertObjectHasAttribute('ourCounteragent', $item);
            $this->assertObjectHasAttribute('creator', $item);
            $this->assertObjectHasAttribute('state', $item);

            $this->assertEquals($item->state, OrderComponent::STATE_TRANSIT);
        }

        /////////////////////////////////////////////////////////

        $query = new Query\GetSupplierWithInvoicesQuery(['state' => OrderComponent::STATE_WAYBILL, 'supplyId' => $id,]);
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertTrue($item instanceof SupplierWithInvoices);

            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('date', $item);
            $this->assertObjectHasAttribute('point', $item);
            $this->assertObjectHasAttribute('sum', $item);
            $this->assertObjectHasAttribute('quantity', $item);
            $this->assertObjectHasAttribute('supplierCounteragentId', $item);
            $this->assertObjectHasAttribute('ourCounteragent', $item);
            $this->assertObjectHasAttribute('creator', $item);
            $this->assertObjectHasAttribute('state', $item);
            $this->assertObjectHasAttribute('waybillNumber', $item);
            $this->assertObjectHasAttribute('waybillDate', $item);

            $this->assertEquals($item->state, OrderComponent::STATE_WAYBILL);
        }
    }

    public function testGetSupplyItemsForShippingAction()
    {
        $id = 35663;

        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetSupplyItemsForShippingQuery(['id' => $id,]);
        $queryBus->handle($query, $result);

        $this->assertArrayHasKey('products', $result);
        $this->assertArrayHasKey('orderItems', $result);
        $this->assertTrue(count($result['products']) > 0);
        $this->assertTrue(count($result['orderItems']) > 0);

        $product = array_shift($result['products']);
        $orderItem = array_shift($result['orderItems']);

        $this->assertTrue($product instanceof SupplyItemsForShippingProducts);
        $this->assertTrue($orderItem instanceof SupplyItemsForShippingOrders);

        $this->assertObjectHasAttribute('id', $product);
        $this->assertObjectHasAttribute('purchasePrice', $product);
        $this->assertObjectHasAttribute('bonusPurchasePrice', $product);
        $this->assertObjectHasAttribute('pricelistDiscount', $product);
        $this->assertObjectHasAttribute('code', $product);
        $this->assertObjectHasAttribute('photoUrl', $product);
        $this->assertObjectHasAttribute('quantity', $product);

        $this->assertObjectHasAttribute('id', $orderItem);
        $this->assertObjectHasAttribute('baseProductId', $orderItem);
        $this->assertObjectHasAttribute('orderId', $orderItem);
        $this->assertObjectHasAttribute('orderItemId', $orderItem);
        $this->assertObjectHasAttribute('hasOrder', $orderItem);
        $this->assertObjectHasAttribute('purchasePrice', $orderItem);
        $this->assertObjectHasAttribute('bonusPurchasePrice', $orderItem);
        $this->assertObjectHasAttribute('pricelistDiscount', $orderItem);
        $this->assertObjectHasAttribute('retailPrice', $orderItem);
        $this->assertObjectHasAttribute('quantity', $orderItem);
        $this->assertObjectHasAttribute('client', $orderItem);
        $this->assertObjectHasAttribute('city', $orderItem);
        $this->assertObjectHasAttribute('hasComments', $orderItem);
    }

    public function testGetSupplierInvoiceAction()
    {
        $id = 35663;

        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetSupplierInvoiceQuery(['id' => $id,]);
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        $this->assertTrue($result instanceof SupplierWithInvoices);

        $this->assertObjectHasAttribute('id', $result);
        $this->assertObjectHasAttribute('date', $result);
        $this->assertObjectHasAttribute('point', $result);
        $this->assertObjectHasAttribute('sum', $result);
        $this->assertObjectHasAttribute('quantity', $result);
        $this->assertObjectHasAttribute('supplierCounteragentId', $result);
        $this->assertObjectHasAttribute('ourCounteragent', $result);
        $this->assertObjectHasAttribute('supplierInvoiceNumber', $result);
        $this->assertObjectHasAttribute('creator', $result);
        $this->assertObjectHasAttribute('state', $result);

        $this->assertEquals($result->state, OrderComponent::STATE_FORMING);
    }
}
