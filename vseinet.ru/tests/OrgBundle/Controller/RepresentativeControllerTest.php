<?php

namespace OrgBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use OrgBundle\Bus\Representative\Query;
use OrgBundle\Bus\Representative\Query\DTO\PointsForShipping;
use OrgBundle\Bus\Representative\Query\DTO\RepresentativePoints;
use OrgBundle\Bus\Representative\Query\DTO\RepresentativeReserves;

class RepresentativeControllerTest extends KernelTestCase
{
    public function testGetForShippingAction()
    {
        $supplierId = 112;

        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetPointsForShippingQuery(['supplierId' => $supplierId,]);
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertTrue($item instanceof PointsForShipping);

            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('name', $item);
        }
    }

    public function testGetRepresentativePointsAction()
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetRepresentativePointsQuery(['isRetailOnly' => true,]);
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertTrue($item instanceof RepresentativePoints);

            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('name', $item);
            $this->assertObjectHasAttribute('code', $item);
        }

        $query = new Query\GetRepresentativePointsQuery(['isRetailOnly' => false,]);
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertTrue($item instanceof RepresentativePoints);

            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('name', $item);
            $this->assertObjectHasAttribute('code', $item);
        }
    }

    public function testGetRepresentativeReservesAction()
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetRepresentativeReservesQuery();
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertTrue($item instanceof RepresentativeReserves);

            $this->assertObjectHasAttribute('representativeId', $item);
            $this->assertObjectHasAttribute('reserveAmount', $item);
        }
    }
}
