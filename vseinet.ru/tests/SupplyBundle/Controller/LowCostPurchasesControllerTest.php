<?php

namespace SupplyBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use SupplyBundle\Bus\LowCostPurchases\Query;
use SupplyBundle\Bus\LowCostPurchases\Query\DTO\Products;

class LowCostPurchasesControllerTest extends KernelTestCase
{
    public function testGetCategoriesAction()
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetCategoriesQuery();
        $queryBus->handle($query, $result);

        $this->assertArrayHasKey('list', $result);
        $this->assertArrayHasKey('totalCount', $result);
        $this->assertTrue(count($result['list']) > 0);
        $this->assertTrue($result['totalCount'] > 0);

        $item = array_shift($result['list']);

        $this->assertTrue(is_array($item));

        $this->assertArrayHasKey('id', $item);
        $this->assertArrayHasKey('name', $item);
        $this->assertArrayHasKey('isWarning', $item);
        $this->assertArrayHasKey('childrens', $item);

        $this->assertTrue(is_array($item['childrens']));
    }

    public function testGetProductsAction()
    {
        $categoryId = 2374;

        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetProductsQuery(['categoryId' => $categoryId,]);
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        $item = array_shift($result);
        $this->assertTrue($item instanceof Products);

        $this->assertObjectHasAttribute('id', $item);
        $this->assertObjectHasAttribute('name', $item);
        $this->assertObjectHasAttribute('categoryId', $item);
        $this->assertObjectHasAttribute('categoryName', $item);
        $this->assertObjectHasAttribute('productId', $item);
        $this->assertObjectHasAttribute('supplierPrice', $item);
        $this->assertObjectHasAttribute('supplierId', $item);
        $this->assertObjectHasAttribute('supplierCode1', $item);
        $this->assertObjectHasAttribute('prevPrice', $item);
        $this->assertObjectHasAttribute('supplierCode2', $item);
        $this->assertObjectHasAttribute('prc', $item);
    }
}
