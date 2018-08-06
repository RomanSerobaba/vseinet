<?php 

namespace ContentBundle\Bus\DetailValueAudit\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetDetailsQueryHandler extends MessageHandler
{
    public function handle(GetDetailsQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\DetailValueAudit\Query\DTO\Category (
                    c.id,
                    c.name,
                    c.pid,
                    true
                )
            FROM ContentBundle:Category c 
            INNER JOIN ContentBundle:DetailGroup dg WITH dg.categoryId = c.id 
            INNER JOIN ContentBundle:Detail d WITH d.groupId = dg.id 
            INNER JOIN ContentBundle:DetailValue dv WITH dv.detailId = d.id
            WHERE dv.createdBy IS NOT NULL AND dv.isVerified = false   
            GROUP BY c.id
            ORDER BY c.name
        ");
        $leafCategories = $q->getResult('IndexByHydrator');

        if (empty($leafCategories)) {
            return new DTO\Details();
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\DetailValueAudit\Query\DTO\Detail (
                    d.id,
                    d.name,
                    dg.categoryId,
                    d.typeCode
                )
                FROM ContentBundle:Detail d  
                INNER JOIN ContentBundle:DetailGroup dg WITH dg.id = d.groupId
                INNER JOIN ContentBundle:DetailValue dv WITH dv.detailId = d.id 
                WHERE dv.createdBy IS NOT NULL AND dv.isVerified = false
                GROUP BY d.id, dg.categoryId
                ORDER BY d.sortOrder
        ");
        $details = $q->getArrayResult();

        foreach ($details as $detail) {
            $leafCategories[$detail->categoryId]->detailIds[] = $detail->id;
        }
        
        $pids = array_map(function($category) { return $category->pid; }, $leafCategories);
        $q = $em->createQuery("
            SELECT
                NEW ContentBundle\Bus\DetailValueAudit\Query\DTO\Category (
                    c.id,
                    c.name
                )
            FROM ContentBundle:Category c 
            WHERE c.id IN (:pids)
            ORDER BY c.name 
        ");
        $q->setParameter('pids', $pids);
        $rootCategories = $q->getResult('IndexByHydrator');

        foreach ($leafCategories as $category) {
            $rootCategories[$category->pid]->categoryIds[] = $category->id;
        }

        return new DTO\Details($rootCategories, $leafCategories, $details);
    }
}