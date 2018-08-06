<?php

namespace TradeMatrixBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use TradeMatrixBundle\Bus\Templates\Query;
use TradeMatrixBundle\Bus\Templates\Query\DTO;

class TemplatesControllerTest extends KernelTestCase
{
    public function testGetCategoryCriteriasAction()
    {
        $id = 1;

        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetCategoryCriteriasQuery(['id' => $id,]);
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        $item = array_shift($result);

        $this->assertTrue($item instanceof DTO\CategoryCriterias);

        $this->assertObjectHasAttribute('name', $item);
        $this->assertObjectHasAttribute('isChecked', $item);
        $this->assertObjectHasAttribute('type', $item);
        $this->assertObjectHasAttribute('detailId', $item);
    }
}
