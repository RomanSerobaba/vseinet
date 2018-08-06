<?php 

namespace OrgBundle\Bus\Representative\Query;

use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Bus\Message\MessageHandler;

class GetIndexQueryHandler extends MessageHandler
{
    public function handle(GetIndexQuery $query)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $sql = '
            SELECT
                w.geo_point_id as id,
                w.geo_point_id,
                w.has_warehouse,
                w.has_retail,
                w.has_order_issueing,
                w.has_delivery,
                w.has_rising,
                w.is_active,
                w.type,
                w.opening_date,
                w.delivery_tax,
                w.is_central
            FROM
                representative w
            WHERE
                w.is_active = TRUE
            ORDER BY
                w.geo_point_id
        ';

        $q = $em->createNativeQuery($sql, new ResultSetMapping());

        $representatives = $q->getResult('ListAssocHydrator');

        foreach ($representatives as &$representative) {
            $sql = '
                SELECT
                    gp.name point,
                    gp.code point_code,
                    gp.geo_address_id,
                    gc.name city,
                    gc.id city_id,
                    gr.id room_id,
                    gr.name room_name,
                    gr.code room_code,
                    gr.type room_type,
                    gr.is_default,
                    gr.has_auto_release,
                    gr.write_off_order
                FROM
                    geo_point gp
                    INNER JOIN geo_city gc ON gc.id = gp.geo_city_id
                    LEFT JOIN geo_room gr ON gr.geo_point_id = gp.id 
                WHERE
                    gp.id = :geo_point_id
            ';

            $q = $em->createNativeQuery($sql, new ResultSetMapping());
            $q->setParameter('geo_point_id', $representative['id']);

            $representative['rooms'] = $q->getResult('ListAssocHydrator');

            $sql = '
                SELECT
                    vup.user_id,
                    vup.fullname
                FROM
                    geo_point gp
                    INNER JOIN user_to_address u2a ON gp.geo_address_id = u2a.geo_address_id
                    INNER JOIN func_view_user_person(u2a.user_id) vup ON vup.user_id = u2a.user_id
                WHERE
                    gp.id = :id
                ORDER BY
                    vup.fullname
            ';

            $q = $em->createNativeQuery($sql, new ResultSetMapping());
            $q->setParameter('id', $representative['id']);

            $representative['managers'] = $q->getResult('ListAssocHydrator');
        }

        return $this->camelizeKeys($representatives);
    }
}