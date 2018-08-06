<?php

namespace ClaimsBundle\Bus\GoodsIssue\Query;

use AppBundle\Bus\Message\MessageHandler;
use ClaimsBundle\Component\GoodsIssueComponent;
use Doctrine\ORM\AbstractQuery;


class GetGoodsIssueQueryHandler extends MessageHandler
{
    /**
     * @param GetGoodsIssueQuery $query
     *
     * @return array
     */
    public function handle(GetGoodsIssueQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $component = new GoodsIssueComponent($em);
        $query2 = new GetGoodsIssuesQuery();
        $query2->id = $query->id;

        return $component->getByFilter($query2)[$query->id];
    }
}