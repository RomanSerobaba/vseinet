<?php

namespace OrderBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use OrgBundle\Bus\Representative\Query;
use OrgBundle\Bus\Representative\Query\DTO\Point;

class RepresentativeControllerTest extends KernelTestCase
{
    public function testGetStoresAction()
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetPointsQuery();
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('name', $item);
        }
    }
}
