<?php 

namespace OrderBundle\Bus\Data\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\ORM\Query\DTORSM;

class GetOrderCommentsQueryHandler extends MessageHandler
{
    public function handle(GetOrderCommentsQuery $query) : array
    {
    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createNativeQuery('
            SELECT
                oc.id /*id*/,
                oc.text /*текст*/,
                vup.fullname AS commentator /*автор*/,
                oc.created_at /*дата*/,
                oc.type /*группа написавшего (франчайзер, менеджер или клиент)*/,
                oc.is_important /*пометка важности*/
            FROM
                order_comment AS oc
                JOIN func_view_user_person(oc.created_by) AS vup ON vup.user_id = oc.created_by 
            WHERE
                oc.order_id = :order_id 
                AND oc.order_item_id IS NULL 
            ORDER BY
                oc.created_at
        ', new DTORSM(\OrderBundle\Bus\Item\Query\DTO\GetComments::class));

        $q->setParameter('order_id', $query->id);

        return $q->getResult('DTOHydrator');
    }
}