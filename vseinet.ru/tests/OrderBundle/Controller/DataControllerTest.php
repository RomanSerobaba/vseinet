<?php

namespace OrderBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use OrderBundle\Bus\Data\Query;
use OrderBundle\Bus\Data\Query\DTO\AnnulCause;

class DataControllerTest extends KernelTestCase
{
    public function testGetAnnulCausesAction()
    {
        $id = 235517;

        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetAnnulCausesQuery(['id' => $id,]);
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertTrue($item instanceof AnnulCause);

            $this->assertObjectHasAttribute('code', $item);
            $this->assertObjectHasAttribute('name', $item);
        }
    }
}
