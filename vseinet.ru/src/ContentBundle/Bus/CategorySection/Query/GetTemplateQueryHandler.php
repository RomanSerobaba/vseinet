<?php 

namespace ContentBundle\Bus\CategorySection\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;
use ContentBundle\Entity\CategorySection;

use ContentBundle\Entity\Detail;
use ContentBundle\Entity\DetailGroup;
use ContentBundle\Entity\DetailValue;
use AppBundle\Enum\DetailType;

class GetTemplateQueryHandler extends MessageHandler
{
    public function handle(GetTemplateQuery $query)
    {   
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($query->categoryId);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория с кодом %d не найдена', $query->categoryId));
        }

        if (0 === $query->id) {
            $section = new CategorySection();
        } else {
            $section = $em->getRepository(CategorySection::class)->find($query->id);
            if (!$section instanceof CategorySection) {
                throw new NotFoundHttpException(sprintf('Раздел категории с кодом %d не найден', $query->id));
            }
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\CategorySection\Query\DTO\Detail (
                    d.id,
                    d.groupId,
                    d.name,
                    d.typeCode,
                    bpn.isRequired,
                    mu.name
                )
            FROM ContentBundle:Detail d 
            INNER JOIN ContentBundle:DetailGroup dg WITH dg.id = d.groupId 
            LEFT OUTER JOIN ContentBundle:BaseProductNaming bpn WITH bpn.detailId = d.id 
            LEFT OUTER JOIN ContentBundle:MeasureUnit mu WITH mu.id = d.unitId 
            WHERE dg.categoryId = :categoryId AND (d.sectionId IS NULL OR d.sectionId = :sectionId) AND d.pid IS NULL
            ORDER BY d.sortOrder  
        ");
        $q->setParameter('categoryId', $query->categoryId);
        $q->setParameter('sectionId', $query->id);
        $details = $q->getResult('IndexByHydrator');

        if (empty($details)) {
            return new DTO\Template();
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\CategorySection\Query\DTO\DetailDepend (
                    d.id,
                    d.pid,
                    d.name,
                    d.typeCode,
                    bpn.isRequired
                )
            FROM ContentBundle:Detail d 
            LEFT OUTER JOIN ContentBundle:BaseProductNaming bpn WITH bpn.detailId = d.pid
            WHERE d.pid IN (:pids)
            ORDER BY d.sortOrder 
        ");
        $q->setParameter('pids', array_keys($details));
        $depends = $q->getResult('IndexByHydrator');
        foreach ($depends as $depend) {
            $details[$depend->pid]->dependIds[] = $depend->id; 
        }

        $enumIds = [];
        $groupIds = [];
        foreach ($details as $detail) {
            if (DetailType::CODE_ENUM == $detail->typeCode) {
                $enumIds[] = $detail->id;
            }
            $groupIds[$detail->groupId] = $detail->groupId;
        }

        if (!empty($enumIds)) {
            $q = $em->createQuery("
                SELECT
                    NEW ContentBundle\Bus\CategorySection\Query\DTO\DetailValue (
                        dv.id,
                        dv.detailId,
                        dv.value
                    )
                FROM ContentBundle:DetailValue dv 
                WHERE dv.detailId IN (:detailIds)
                ORDER BY dv.value 
            ");
            $q->setParameter('detailIds', $enumIds);
            $values = $q->getResult('IndexByHydrator');
            foreach ($values as $value) {
                $details[$value->detailId]->valueIds[] = $value->id;
            }    
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\CategorySection\Query\DTO\DetailGroup (
                    dg.id,
                    dg.name
                )
            FROM ContentBundle:DetailGroup dg 
            WHERE dg.id IN (:ids)
            ORDER BY dg.sortOrder
        ");
        $q->setParameter('ids', $groupIds);
        $groups = $q->getResult('IndexByHydrator');

        foreach ($details as $detail) {
            $groups[$detail->groupId]->detailIds[] = $detail->id;
        }

        return new DTO\Template($groups, $details, $depends, $values);
    }
}