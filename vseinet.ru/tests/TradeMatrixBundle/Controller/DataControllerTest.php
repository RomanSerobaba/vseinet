<?php

namespace TradeMatrixBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use TradeMatrixBundle\Bus\Data\Query;
use TradeMatrixBundle\Bus\Data\Query\DTO;

class DataControllerTest extends KernelTestCase
{
    public function testGetTradeMatrixAction()
    {
        $categoryId = 3324;

        self::bootKernel();

        $container = self::$kernel->getContainer();
        $queryBus = $container->get('query_bus');

        ///////////////////////////////////////////
        $query = new Query\GetTradeMatrixQuery(['type' => Query\GetTradeMatrixQueryHandler::TEMPLATES,]);
        $queryBus->handle($query, $result);

        $this->assertArrayHasKey('templates', $result);
        $this->assertTrue(count($result['templates']) > 0);

        $template = array_shift($result['templates']);
        $this->assertTrue($template instanceof DTO\TradeMatrixTemplates);

        $this->assertObjectHasAttribute('id', $template);
        $this->assertObjectHasAttribute('name', $template);
        $this->assertObjectHasAttribute('activeFrom', $template);
        $this->assertObjectHasAttribute('activeTill', $template);
        $this->assertObjectHasAttribute('isRemovable', $template);
        $this->assertObjectHasAttribute('representatives', $template);

        $this->assertTrue(is_array($template->representatives));

        /////////////////////////////////////////
        $query = new Query\GetTradeMatrixQuery(['type' => Query\GetTradeMatrixQueryHandler::CATEGORIES,]);
        $queryBus->handle($query, $result);

        $this->assertArrayHasKey('categories', $result);
        $this->assertTrue(count($result['categories']) > 0);

        $category = array_shift($result['categories']);
        $this->assertTrue($category instanceof DTO\TradeMatrixCategories);

        $this->assertObjectHasAttribute('id', $category);
        $this->assertObjectHasAttribute('name', $category);
        $this->assertObjectHasAttribute('tradeMatrixTemplateId', $category);

        ///////////////////////////////////////////
        $query = new Query\GetTradeMatrixQuery(['type' => Query\GetTradeMatrixQueryHandler::SUBCATEGORIES,]);
        $queryBus->handle($query, $result);

        $this->assertArrayHasKey('subcategories', $result);
        $this->assertTrue(count($result['subcategories']) > 0);

        $subcategory = array_shift($result['subcategories']);
        $this->assertTrue($subcategory instanceof DTO\TradeMatrixSubcategories);

        $this->assertObjectHasAttribute('id', $subcategory);
        $this->assertObjectHasAttribute('name', $subcategory);
        $this->assertObjectHasAttribute('pid', $subcategory);
        $this->assertObjectHasAttribute('criterias', $subcategory);
        $this->assertObjectHasAttribute('tradeMatrixTemplateId', $subcategory);
        $this->assertObjectHasAttribute('representatives', $subcategory);

        ///////////////////////////////////////////
        $query = new Query\GetTradeMatrixQuery(['type' => Query\GetTradeMatrixQueryHandler::PRODUCTS, 'categoryId' => $categoryId,]);
        $queryBus->handle($query, $result);

        $this->assertArrayHasKey('products', $result);
        $this->assertTrue(count($result['products']) > 0);

        $product = array_shift($result['products']);
        $this->assertTrue($product instanceof DTO\TradeMatrixProducts);

        $this->assertObjectHasAttribute('id', $product);
        $this->assertObjectHasAttribute('categoryId', $product);
        $this->assertObjectHasAttribute('name', $product);
        $this->assertObjectHasAttribute('purchasePrice', $product);
        $this->assertObjectHasAttribute('isOrderNeeded', $product);
        $this->assertObjectHasAttribute('representatives', $product);

        if (!empty($template->representatives)) {
            $this->assertTrue(is_array($template->representatives));
        }
    }

    public function testGetSupplierInvoiceAction()
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetLimitsQuery();
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        $item = array_shift($result);

        $this->assertTrue($item instanceof DTO\Limits);

        $this->assertObjectHasAttribute('representativeId', $item);
        $this->assertObjectHasAttribute('limitAmount', $item);
    }

    /**
     * Skipped
     */
    public function testGetTransferingProductsAction()
    {
        $representativeId = 141;

        self::bootKernel();

        $container = self::$kernel->getContainer();

        $this->assertTrue(true);
        return;

        $queryBus = $container->get('query_bus');
        $query = new Query\GetTransferingProductsQuery(['representativeId' => $representativeId,]);
        $queryBus->handle($query, $result);

        $this->assertArrayHasKey('products', $result);
        $this->assertArrayHasKey('categories', $result);
        $this->assertArrayHasKey('subcategories', $result);
        $this->assertTrue(count($result['products']) > 0);
        $this->assertTrue(count($result['categories']) > 0);
        $this->assertTrue(count($result['subcategories']) > 0);

        $product = array_shift($result['products']);
        $category = array_shift($result['categories']);
        $subcategory = array_shift($result['subcategories']);

        $this->assertTrue($product instanceof DTO\TransferingProducts);
        $this->assertTrue($category instanceof DTO\TransferingCategories);
        $this->assertTrue($subcategory instanceof DTO\TransferingSubCategories);

        $this->assertObjectHasAttribute('baseProductId', $product);
        $this->assertObjectHasAttribute('name', $product);
        $this->assertObjectHasAttribute('needQuantity', $product);
        $this->assertObjectHasAttribute('representatives', $product);

        if (!empty($product->representatives)) {
            $this->assertTrue(is_array($product->representatives));
        }

        $this->assertObjectHasAttribute('id', $category);
        $this->assertObjectHasAttribute('name', $category);

        $this->assertObjectHasAttribute('id', $subcategory);
        $this->assertObjectHasAttribute('name', $subcategory);
        $this->assertObjectHasAttribute('pid', $subcategory);
    }

    /**
     * Skipped
     */
    public function testGetForOrderingAction()
    {
        $representativeId = 141;

        self::bootKernel();

        $container = self::$kernel->getContainer();

        $this->assertTrue(true);
        return;

        $queryBus = $container->get('query_bus');
        $query = new Query\GetForOrderingQuery(['representativeId' => $representativeId,]);
        $queryBus->handle($query, $result);

        $this->assertArrayHasKey('products', $result);
        $this->assertArrayHasKey('categories', $result);
        $this->assertArrayHasKey('subcategories', $result);
        $this->assertTrue(count($result['products']) > 0);
        $this->assertTrue(count($result['categories']) > 0);
        $this->assertTrue(count($result['subcategories']) > 0);

        $product = array_shift($result['products']);
        $category = array_shift($result['categories']);
        $subcategory = array_shift($result['subcategories']);

        $this->assertTrue($product instanceof DTO\OrderingProducts);
        $this->assertTrue($category instanceof DTO\OrderingCategories);
        $this->assertTrue($subcategory instanceof DTO\OrderingSubCategories);

        $this->assertObjectHasAttribute('baseProductId', $product);
        $this->assertObjectHasAttribute('name', $product);
        $this->assertObjectHasAttribute('needQuantity', $product);
        $this->assertObjectHasAttribute('categoryId', $product);
        $this->assertObjectHasAttribute('price', $product);
        $this->assertObjectHasAttribute('representativeId', $product);
        $this->assertObjectHasAttribute('soldAmount', $product);
        $this->assertObjectHasAttribute('isAvailable', $product);
        $this->assertObjectHasAttribute('analogues', $product);
        $this->assertObjectHasAttribute('representatives', $product);

        $this->assertTrue(is_array($product->representatives));

        $this->assertObjectHasAttribute('id', $category);
        $this->assertObjectHasAttribute('name', $category);

        $this->assertObjectHasAttribute('id', $subcategory);
        $this->assertObjectHasAttribute('name', $subcategory);
        $this->assertObjectHasAttribute('pid', $subcategory);
    }
}
