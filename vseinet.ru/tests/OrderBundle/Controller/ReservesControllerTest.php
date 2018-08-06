<?php

namespace OrderBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use OrderBundle\Bus\Reserves\Query;
use OrderBundle\Bus\Reserves\Query\DTO\ReservePointsQuery;

class ReservesControllerTest extends KernelTestCase
{
    public function testGetAvailableVariantsAction()
    {
        $id = 731508;

        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetReservePointsQuery(['id' => $id,]);
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertTrue($item instanceof ReservePointsQuery);

            $this->assertObjectHasAttribute('pointId', $item);
            $this->assertObjectHasAttribute('quantity', $item);
            $this->assertObjectHasAttribute('shopQuantity', $item);
            $this->assertObjectHasAttribute('city', $item);
            $this->assertObjectHasAttribute('pointCode', $item);
            $this->assertObjectHasAttribute('isInTransit', $item);
            $this->assertObjectHasAttribute('reservedQuantity', $item);

            $this->assertEquals($item->pointId, 344);
            $this->assertEquals($item->city, 'Чистополь');
            $this->assertEquals($item->pointCode, 'П-ГОР');
        }
    }
}
