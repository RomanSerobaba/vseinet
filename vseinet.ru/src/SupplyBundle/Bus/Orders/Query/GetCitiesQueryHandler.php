<?php 

namespace SupplyBundle\Bus\Orders\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\RepresentativeTypeCode;
use Doctrine\ORM\Query\ResultSetMapping;

class GetCitiesQueryHandler extends MessageHandler
{
    /**
     * @param GetCitiesQuery $query
     *
     * @return array
     */
    public function handle(GetCitiesQuery $query) : array
    {
    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createNativeQuery('
            SELECT
                geo_city.id,
                geo_city.name,
                CASE 
                    WHEN representative.type = :our THEN \'Представительства\' 
                    WHEN representative.type = :partner THEN \'Партнерские точки\' 
                    WHEN representative.type = :franchiser THEN \'Франчайзинг\' 
                    ELSE \'Торговка\' 
                END AS type 
            FROM
                representative
                JOIN geo_point ON geo_point.id = representative.geo_point_id
                JOIN geo_city ON geo_city.id = geo_point.geo_city_id 
            WHERE
                representative.is_active = TRUE 
                AND representative.has_retail = TRUE 
            GROUP BY
                representative.type,
                geo_city.id 
            ORDER BY
                CASE 
                    WHEN representative.TYPE = :our THEN 0 
                    WHEN representative.TYPE = :partner THEN 1 
                    WHEN representative.TYPE = :franchiser THEN 2 
                    ELSE 3 
                END,
                geo_city.name
        ', new ResultSetMapping());

        $q->setParameter('our', RepresentativeTypeCode::OUR);
        $q->setParameter('partner', RepresentativeTypeCode::PARTNER);
        $q->setParameter('franchiser', RepresentativeTypeCode::FRANCHISER);

        return $this->camelizeKeys($q->getResult('ListAssocHydrator'));
    }
}