<?php

namespace OrgBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use OrgBundle\Bus\Suggestions\Query;
use OrgBundle\Bus\Suggestions\Query\DTO\Suggestion;
use OrgBundle\Bus\Suggestions\Query\DTO\SuggestionComment;

class SuggestionsControllerTest extends KernelTestCase
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
             * @var Suggestion $item
             */
            $item = array_shift($result['items']);

            $this->assertTrue($item instanceof Suggestion);

            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('text', $item);
            $this->assertObjectHasAttribute('createdAt', $item);
            $this->assertObjectHasAttribute('createdBy', $item);
            $this->assertObjectHasAttribute('firstname', $item);
            $this->assertObjectHasAttribute('phone', $item);
            $this->assertObjectHasAttribute('email', $item);
            $this->assertObjectHasAttribute('isChecked', $item);
            $this->assertObjectHasAttribute('comments', $item);

            if (!empty($item->comments)) {
                $comment = array_shift($item->comments);
                $this->assertTrue($comment instanceof SuggestionComment);

                $this->assertObjectHasAttribute('id', $comment);
                $this->assertObjectHasAttribute('clientSuggestionId', $comment);
                $this->assertObjectHasAttribute('text', $comment);
                $this->assertObjectHasAttribute('fullname', $comment);
                $this->assertObjectHasAttribute('createdAt', $comment);
                $this->assertObjectHasAttribute('createdBy', $comment);
            }
        }
    }
}
