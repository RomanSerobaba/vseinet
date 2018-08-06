<?php 

namespace PricingBundle\Bus\Product\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\CompetitorTypeCode;
use AppBundle\Enum\RepresentativeTypeCode;
use AppBundle\ORM\Query\DTORSM;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery('
            SELECT
                NEW PricingBundle\Bus\Product\Query\DTO\Product (
                    p.id,
                    p.baseProductId,
                    p.geoCityId,
                    p.price                    
                )
            FROM
                PricingBundle:Product as p                
            WHERE
                p.baseProductId = :baseProductId
                AND p.geoCityId ' . (null === $query->cityId ? ' IS NULL' : '= :cityId') . '
        ');

        $q->setParameter('baseProductId', $query->baseProductId);
        
        if (null !== $query->cityId) {
            $q->setParameter('cityId', $query->cityId);
        }

        return $q->getArrayResult();
    }
}