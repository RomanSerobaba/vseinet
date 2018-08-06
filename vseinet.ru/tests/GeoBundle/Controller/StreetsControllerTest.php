<?php

namespace GeoBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use GeoBundle\Bus\Streets\Query;
use GeoBundle\Bus\Streets\Query\DTO\Street;

class StreetsControllerTest extends KernelTestCase
{
    public function testGetAction()
    {
        $cityId = 14927;
        $name = 'Кольц';
        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\SearchStreetsQuery(['cityId' => $cityId, 'q' => $name,]);
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertTrue($item instanceof Street);

            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('name', $item);
        }
    }
}
