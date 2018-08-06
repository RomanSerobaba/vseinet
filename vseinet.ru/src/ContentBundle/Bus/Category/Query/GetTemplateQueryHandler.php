<?php 

namespace ContentBundle\Bus\Category\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;
use ContentBundle\Entity\Detail;
use ContentBundle\Entity\DetailGroup;
use ContentBundle\Entity\DetailValue;
use AppBundle\Enum\DetailType;

class GetTemplateQueryHandler extends MessageHandler
{
    public function handle(GetTemplateQuery $query)
    {   
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($query->id);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $query->id));
        }

        $sections[0] = new DTO\CategorySection(0, 'Общий', $category->getId(), $category->getBasename(), $category->getGender());
        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Category\Query\DTO\CategorySection (
                    cs.id,
                    cs.name,
                    cs.categoryId,
                    cs.basename,
                    cs.gender
                )
            FROM ContentBundle:CategorySection cs
            WHERE cs.categoryId = :categoryId
            ORDER BY cs.name
        ");
        $q->setParameter('categoryId', $category->getId());
        $sections += $q->getResult('IndexByHydrator');

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Category\Query\DTO\DetailGroup (
                    dg.id,
                    dg.name,
                    dg.categoryId
                )
            FROM ContentBundle:DetailGroup dg 
            WHERE dg.categoryId = :categoryId
            ORDER BY dg.sortOrder
        ");
        $q->setParameter('categoryId', $category->getId());
        $groups = $q->getResult('IndexByHydrator');
        if (empty($groups)) {
            return new DTO\Template($sections);
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Category\Query\DTO\Detail (
                    d.id,
                    d.groupId,
                    d.sectionId,
                    d.name,
                    d.typeCode,
                    bpn.isRequired,
                    mu.name
                )
            FROM ContentBundle:Detail d 
            LEFT OUTER JOIN ContentBundle:BaseProductNaming bpn WITH bpn.detailId = d.id 
            LEFT OUTER JOIN ContentBundle:MeasureUnit mu WITH mu.id = d.unitId 
            WHERE d.groupId IN (:groupIds) AND d.pid IS NULL
            ORDER BY d.sortOrder 
        ");
        $q->setParameter('groupIds', array_keys($groups));
        $details = $q->getResult('IndexByHydrator');

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Category\Query\DTO\DetailDepend (
                    d.id,
                    d.pid,
                    d.name
                )
            FROM ContentBundle:Detail d 
            WHERE d.groupId IN (:groupIds) AND d.pid IS NOT NULL
            ORDER BY d.sortOrder 
        ");
        $q->setParameter('groupIds', array_keys($groups));
        $depends = $q->getArrayResult();
        foreach ($depends as $depend) {
            $details[$depend->pid]->dependIds[] = $depend->id; 
        }

        $enumIds = [];
        foreach ($details as $detail) {      
            if (DetailType::CODE_ENUM === $detail->typeCode) {
                $enumIds[] = $detail->id;
            } 
            if ($detail->sectionId) {
                $sections[$detail->sectionId]->detailIdsByGroupIds[$detail->groupId][] = $detail->id; 
            }
            else {
                $sections[0]->detailIdsByGroupIds[$detail->groupId][] = $detail->id;
            }
        }
        if (empty($enumIds)) {
            $values = [];
        }
        else {
            $q = $em->createQuery("
                SELECT
                    NEW ContentBundle\Bus\Category\Query\DTO\DetailValue (
                        dv.id,
                        dv.detailId,
                        dv.value
                    )
                FROM ContentBundle:DetailValue dv 
                WHERE dv.detailId IN (:detailIds)
                ORDER BY dv.value 
            ");
            $q->setParameter('detailIds', $enumIds);
            $values = $q->getArrayResult();
            foreach ($values as $value) {
                $details[$value->detailId]->valueIds[] = $value->id;
            }
        }

        return new DTO\Template($sections, $groups, $details, $depends, $values);
    }
}