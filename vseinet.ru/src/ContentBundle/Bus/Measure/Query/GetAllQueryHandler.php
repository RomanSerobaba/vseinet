<?php 

namespace ContentBundle\Bus\Measure\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetAllQueryHandler extends MessageHandler
{
    public function handle(GetAllQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Measure\Query\DTO\Measure (
                    m.id, 
                    m.name,
                    CASE WHEN EXISTS (
                        SELECT 1 
                        FROM ContentBundle:MeasureUnit u 
                        INNER JOIN ContentBundle:Detail d WITH d.unitId = u.id   
                        WHERE u.measureId = m.id
                    ) 
                    THEN true ELSE false END
                )
            FROM ContentBundle:Measure m 
            ORDER BY m.name 
        ");
        $measures = $q->getResult('IndexByHydrator');

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Measure\Query\DTO\MeasureUnit (
                    mu.id, 
                    mu.measureId,
                    mu.name,
                    mu.k,
                    mu.useSpace,
                    GROUP_CONCAT(mua.name SEPARATOR ','),
                    CASE WHEN EXISTS (
                        SELECT 1 
                        FROM ContentBundle:Detail d 
                        WHERE d.unitId = mu.id   
                    ) 
                    THEN true ELSE false END
                )
            FROM ContentBundle:MeasureUnit mu 
            LEFT OUTER JOIN ContentBundle:MeasureUnitAlias mua WITH mua.unitId = mu.id 
            GROUP BY mu.id
            ORDER BY mu.name 
        ");
        $units = $q->getArrayResult();

        foreach ($units as $unit) {
            $measures[$unit->measureId]->unitIds[] = $unit->id;
        }

        return new DTO\All($measures, $units);
    }
}