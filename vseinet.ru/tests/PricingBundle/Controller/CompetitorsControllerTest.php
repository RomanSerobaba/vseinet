<?php

namespace PricingBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use PricingBundle\Bus\Competitors\Query;
use PricingBundle\Bus\Competitors\Query\DTO\{Cities, GetList, RevisionProducts, RevisionCategories};

class CompetitorsControllerTest extends KernelTestCase
{
    public function testGetCitiesAction()
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetCitiesQuery();
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertTrue($item instanceof Cities);

            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('name', $item);
            $this->assertObjectHasAttribute('looseCount', $item);
        }
    }

    public function testListAction()
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetListQuery();
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertTrue($item instanceof GetList);

            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('name', $item);
            $this->assertObjectHasAttribute('link', $item);
            $this->assertObjectHasAttribute('isActive', $item);
            $this->assertObjectHasAttribute('checkingCount', $item);
            $this->assertObjectHasAttribute('failedCount', $item);
            $this->assertObjectHasAttribute('successfulCount', $item);
            $this->assertObjectHasAttribute('competitiveCount', $item);
            $this->assertObjectHasAttribute('loosingCount', $item);
        }
    }

    public function testGetRevisionAction()
    {
        $id = 19;
        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetRevisionQuery(['id' => $id,]);
        $queryBus->handle($query, $result);

        $this->assertArrayHasKey('products', $result);
        $this->assertTrue(count($result['products']) > 0);

        $product = array_shift($result['products']);
        $this->assertTrue($product instanceof RevisionProducts);

        $this->assertObjectHasAttribute('categoryId', $product);
        $this->assertObjectHasAttribute('baseProductId', $product);
        $this->assertObjectHasAttribute('name', $product);
        $this->assertObjectHasAttribute('priceTime', $product);
        $this->assertObjectHasAttribute('purchasePrice', $product);
        $this->assertObjectHasAttribute('retailPrice', $product);
        $this->assertObjectHasAttribute('createdBy', $product);
        $this->assertObjectHasAttribute('link', $product);
        $this->assertObjectHasAttribute('competitors', $product);

        $this->assertTrue(is_array($product->competitors));

        ///////////////////////////////////////////////////////////////////////

        $this->assertArrayHasKey('categories', $result);
        $this->assertTrue(count($result['categories']) > 0);

        $category = array_shift($result['categories']);
        $this->assertTrue($category instanceof RevisionCategories);

        $this->assertObjectHasAttribute('id', $category);
        $this->assertObjectHasAttribute('name', $category);
    }
}
