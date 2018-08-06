<?php

namespace SupplyBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use SupplyBundle\Bus\Suppliers\Query;
use SupplyBundle\Bus\Suppliers\Query\DTO\{ SuppliersForOrdersProcessing, Suppliers, CounteragentsForSupply, SuppliersForSelect };

class SuppliersControllerTest extends KernelTestCase
{
    public function testGetProcessingAction()
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
//        $commandBus = $container->get('command_bus');

        // auth
//        $commandBus->handle(new Command\LoginCommand(['clientId' => 1, 'username' => '9374266901', 'password' => '111',]));
//        $queryBus->handle(new AppQuery\GetAccessTokenQuery(), $token);

        $query = new Query\GetProcessingQuery(['isTest' => true,]);
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertTrue($item instanceof SuppliersForOrdersProcessing);

            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('name', $item);
            $this->assertObjectHasAttribute('managerId', $item);
            $this->assertObjectHasAttribute('processingItemsQuantity', $item);
            $this->assertObjectHasAttribute('supplierReserveId', $item);
        }

//        $commandBus->handle(new Command\LogoutCommand());
    }

    public function testSuppliersAction()
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetShippingQuery(['isTest' => true,]);
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertTrue($item instanceof Suppliers);

            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('name', $item);
            $this->assertObjectHasAttribute('managerId', $item);
            $this->assertObjectHasAttribute('goodsQuantity', $item);
            $this->assertObjectHasAttribute('suppliesQuantity', $item);
            $this->assertObjectHasAttribute('orderThresholdTime', $item);
            $this->assertObjectHasAttribute('orderDeliveryTime', $item);
            $this->assertObjectHasAttribute('isShipping', $item);
        }
    }

    public function testGetCounteragentsForSupplyAction()
    {
        $id = 35663;

        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetCounteragentsForSupplyQuery(['supplyId' => $id,]);
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertTrue($item instanceof CounteragentsForSupply);

            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('name', $item);
        }
    }

    public function testGetForSelectAction()
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetForSelectQuery();
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertTrue($item instanceof SuppliersForSelect);

            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('code', $item);
        }
    }
}
