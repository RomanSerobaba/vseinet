<?php

namespace OrgBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use OrgBundle\Bus\Complaint\Query;
use OrgBundle\Bus\Complaint\Query\DTO\Complaint;
use OrgBundle\Bus\Complaint\Query\DTO\ComplaintComment;

class ComplaintControllerTest extends KernelTestCase
{
    public function testIndexAction()
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetIndexQuery(['isAll' => true, 'limit' => 10, 'lastId' => 754,]);
        $queryBus->handle($query, $result);

        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('pageCount', $result);
        $this->assertTrue(count($result['items']) > 0);
        $this->assertTrue($result['pageCount'] > 0);

        if (count($result['items']) > 0) {
            /**
             * @var Complaint $item
             */
            $item = array_shift($result['items']);

            $this->assertTrue($item instanceof Complaint);

            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('text', $item);
            $this->assertObjectHasAttribute('createdAt', $item);
            $this->assertObjectHasAttribute('createdBy', $item);
            $this->assertObjectHasAttribute('firstname', $item);
            $this->assertObjectHasAttribute('phone', $item);
            $this->assertObjectHasAttribute('email', $item);
            $this->assertObjectHasAttribute('isChecked', $item);
            $this->assertObjectHasAttribute('manager', $item);
            $this->assertObjectHasAttribute('managerPhone', $item);
            $this->assertObjectHasAttribute('type', $item);
            $this->assertObjectHasAttribute('comments', $item);

            if (!empty($item->comments)) {
                $comment = array_shift($item->comments);
                $this->assertTrue($comment instanceof ComplaintComment);

                $this->assertObjectHasAttribute('id', $comment);
                $this->assertObjectHasAttribute('complaintId', $comment);
                $this->assertObjectHasAttribute('text', $comment);
                $this->assertObjectHasAttribute('fullname', $comment);
                $this->assertObjectHasAttribute('createdAt', $comment);
                $this->assertObjectHasAttribute('createdBy', $comment);
            }
        }
    }
}
