<?php

namespace PromoBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use PromoBundle\Bus\ProductReview\Query;
use PromoBundle\Bus\ProductReview\Query\DTO\ProductReview;

class ProductReviewControllerTest extends KernelTestCase
{
    public function testIndexAction()
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetIndexQuery(['isAll' => true, 'limit' => 10,]);
        $queryBus->handle($query, $result);

        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('pageCount', $result);
        $this->assertTrue(count($result['items']) > 0);
        $this->assertTrue($result['pageCount'] > 0);

        if (count($result['items']) > 0) {
            /**
             * @var ProductReview $item
             */
            $item = array_shift($result['items']);

            $this->assertTrue($item instanceof ProductReview);

            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('comment', $item);
            $this->assertObjectHasAttribute('createdAt', $item);
            $this->assertObjectHasAttribute('createdBy', $item);
            $this->assertObjectHasAttribute('advantages', $item);
            $this->assertObjectHasAttribute('disadvantages', $item);
            $this->assertObjectHasAttribute('estimate', $item);
            $this->assertObjectHasAttribute('name', $item);
            $this->assertObjectHasAttribute('baseProductId', $item);
            $this->assertObjectHasAttribute('approvedAt', $item);
            $this->assertObjectHasAttribute('approvedBy', $item);
            $this->assertObjectHasAttribute('contacts', $item);
        }
    }
}
