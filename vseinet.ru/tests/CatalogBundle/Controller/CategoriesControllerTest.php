<?php

namespace CatalogBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use CatalogBundle\Bus\Categories\Query;
use CatalogBundle\Bus\Categories\Query\DTO\{RootFilter, EmployeesFilter};

class CategoriesControllerTest extends KernelTestCase
{
    public function testRootFilterAction()
    {
        $competitorId = 26;

        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetRootFilterQuery(['competitorId' => $competitorId,]);
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertTrue($item instanceof RootFilter);

            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('name', $item);
        }
    }

    public function testEmployeesFilterAction()
    {
        $competitorId = 20;

        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetEmployeesFilterQuery(['competitorId' => $competitorId,]);
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertTrue($item instanceof EmployeesFilter);

            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('fullname', $item);
        }
    }
}
