<?php

namespace SupplyBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use SupplyBundle\Bus\Invoices\Query;
use SupplyBundle\Bus\Invoices\Query\DTO\SupplyPoints;

class InvoicesControllerTest extends KernelTestCase
{
    public function testGetListAction()
    {
        $id = 35681;

        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetSupplyPointsQuery(['id' => $id,]);
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertTrue($item instanceof SupplyPoints);

            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('name', $item);
        }
    }
}
