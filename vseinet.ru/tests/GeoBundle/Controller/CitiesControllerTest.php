<?php

namespace GeoBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use GeoBundle\Bus\Cities\Query;
use GeoBundle\Bus\Cities\Query\DTO\City;

class CitiesControllerTest extends KernelTestCase
{
    public function testGetAction()
    {
        $name = 'Москв';

        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\SearchCitiesQuery(['q' => $name,]);
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertTrue($item instanceof City);

            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('name', $item);
        }
    }
}
