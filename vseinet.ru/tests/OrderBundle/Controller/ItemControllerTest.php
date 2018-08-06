<?php

namespace OrderBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use OrderBundle\Bus\Item\Query;
use OrderBundle\Bus\Item\Query\DTO\GetComments;

class ItemControllerTest extends KernelTestCase
{
    public function testCommentsAction()
    {
        $id = 235517;

        self::bootKernel();

        $container = self::$kernel->getContainer();

        $queryBus = $container->get('query_bus');
        $query = new Query\GetCommentsQuery(['id' => $id,]);
        $queryBus->handle($query, $result);

        $this->assertTrue(count($result) > 0);

        if (count($result) > 0) {
            $item = array_shift($result);

            $this->assertTrue($item instanceof GetComments);

            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('orderItemId', $item);
            $this->assertObjectHasAttribute('text', $item);
            $this->assertObjectHasAttribute('createdAt', $item);
            $this->assertObjectHasAttribute('createdBy', $item);
            $this->assertObjectHasAttribute('type', $item);
            $this->assertObjectHasAttribute('isImportant', $item);
            $this->assertObjectHasAttribute('isCommon', $item);
            $this->assertObjectHasAttribute('commentator', $item);

            $this->assertEquals($item->text, 'везите гипопотама!!!');
            $this->assertEquals($item->type, 'manager');
            $this->assertEquals($item->commentator, 'Лунёв Денис Дмитриевич');
        }
    }
}
