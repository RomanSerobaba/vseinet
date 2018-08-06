<?php

namespace SupplyBundle\Tests\Controller;

use SupplyBundle\Bus\Reserves\Query;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use SupplyBundle\Bus\Order\Query\DTO\OrderProducts;
use SupplyBundle\Bus\Order\Query\DTO\OrderItems;

class ReservesControllerTest extends KernelTestCase
{
    public function testGetProcessingItemsAction()
    {
        $supplierReserveId = 317;

        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetIndexQuery(['id' => $supplierReserveId,]);
        $queryBus->handle($query, $result);

        $this->assertArrayHasKey('products', $result);
        $this->assertArrayHasKey('orderItems', $result);
        $this->assertTrue(count($result['products']) > 0);
        $this->assertTrue(count($result['orderItems']) > 0);

        $product = $result['products'][0];
        $orderItem = $result['orderItems'][0];

        $this->assertTrue($product instanceof OrderProducts);
        $this->assertTrue($orderItem instanceof OrderItems);

        $this->assertTrue(property_exists($product, 'id'));
        $this->assertTrue(property_exists($product, 'code'));
        $this->assertTrue(property_exists($product, 'name'));
        $this->assertTrue(property_exists($product, 'photoUrl'));
        $this->assertTrue(property_exists($product, 'needQuantity'));

        $this->assertTrue(property_exists($orderItem, 'id'));
        $this->assertTrue(property_exists($orderItem, 'baseProductId'));
        $this->assertTrue(property_exists($orderItem, 'orderItemId'));
        $this->assertTrue(property_exists($orderItem, 'orderId'));
        $this->assertTrue(property_exists($orderItem, 'purchasePrice'));
        $this->assertTrue(property_exists($orderItem, 'retailPrice'));
        $this->assertTrue(property_exists($orderItem, 'clientName'));
        $this->assertTrue(property_exists($orderItem, 'city'));
    }
}
