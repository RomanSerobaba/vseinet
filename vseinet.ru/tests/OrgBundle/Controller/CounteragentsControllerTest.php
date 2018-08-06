<?php

namespace OrgBundle\Tests\Controller;

use SupplyBundle\Component\OrderComponent;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use OrgBundle\Bus\Counteragents\Query;
use OrgBundle\Bus\Counteragents\Query\DTO\OurCounteragents;

class CounteragentsControllerTest extends KernelTestCase
{
    public function testGetTreeAction()
    {
        $supplierId = 112;

        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetOurCounteragentsQuery(['supplierId' => $supplierId,]);
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertTrue($item instanceof OurCounteragents);

            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('name', $item);
        }
    }
}
