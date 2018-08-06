<?php

namespace ClaimsBundle\Bus\GoodsIssue\Query;

use AppBundle\Bus\Message\MessageHandler;
use ClaimsBundle\Component\GoodsIssueComponent;
use Doctrine\ORM\AbstractQuery;


class GetGoodsIssuesQueryHandler extends MessageHandler
{
    /**
     * @param GetGoodsIssuesQuery $query
     *
     * @return array
     */
    public function handle(GetGoodsIssuesQuery $query): array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $component = new GoodsIssueComponent($em);

        return [
            'balance' => $component->getMonthlyBalance($em),
            'total' => $component->buildDefectStatistics([
                'params' => [
                    'till' => date('Y-m-d H:i:s'),
                    'warehouse' => $query->warehouse,
                ],
                'clause' => $query->warehouse ? 'd.supplier_id IN (:warehouse)' : '',
                'from' => '',
            ])[0]['value'],
            'list' => $component->getByFilter($query),
            'types' => $component->getGoodIssuesTypes(),
            'rooms' => $component->getRooms(),
            'suppliers' => $component->getSuppliers(),
            'services' => $component->getServiceCenters(),
        ];
    }


}