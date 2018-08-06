<?php 

namespace SiteBundle\Bus\Catalog\Finder\Block;

use Doctrine\ORM\EntityManager;
use SiteBundle\Bus\Catalog\Query\DTO\Filter\CategorySection;

class CategorySections
{
    /**
     * @return array<SiteBundle\Bus\Catalog\Query\DTO\Filter\CategorySection>
     */
    public static function build(array $section2count, EntityManager $em): array 
    {
        if (!empty($section2count)) {
            $q = $em->createQuery("
                SELECT 
                    NEW SiteBundle\Bus\Catalog\Query\DTO\Filter\CategorySection (
                        cs.id, 
                        cs.name 
                    )
                FROM ContentBundle:CategorySection cs 
                WHERE cs.id IN (:ids) 
            ");
            $q->setParameter('ids', array_keys($section2count));
            $sections = $q->getResult('IndexByHydrator');
        }
        $sections[0] = new CategorySection(0, '');
        foreach ($section2count as $id => $count) {
            if (isset($sections[$id])) {
                $sections[$id]->countProducts = $count;
            }
        }

        return $sections;
    }
}
