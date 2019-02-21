<?php 

namespace AppBundle\Bus\Catalog\Finder\Block;

use Doctrine\ORM\EntityManager;
use AppBundle\Bus\Catalog\Query\DTO\Filter\Category;

class Categories
{
    /**
     * @return array<Category>
     */
    public static function build(array $categoryId2count, EntityManager $em): array 
    {
        $q = $em->createQuery("
            SELECT 
                c.id,
                c.name,
                c.isTplEnabled, 
                c2.id AS id2,
                c2.name AS name2,
                CASE WHEN c.isTplEnabled = true THEN 1 ELSE 2 END AS HIDDEN ORD1,
                CASE WHEN c.isTplEnabled = true THEN c.name ELSE c2.name END AS HIDDEN ORD2
            FROM AppBundle:Category c 
            INNER JOIN AppBundle:CategoryPath cp WITH cp.id = c.id AND cp.plevel = 2
            INNER JOIN AppBundle:Category c2 WITH c2.id = cp.pid 
            WHERE c.id IN (:ids)
            ORDER BY ORD1, ORD2, c.name  
        ");
        $q->setParameter('ids', array_keys($categoryId2count));
        $categories = $q->getResult();

        $main = [];
        $tree = [];
        foreach ($categories as $category) {
            $id = $category['id'];
            if ($category['isTplEnabled']) {
                $main[$id] = new Category($id, $category['name'], $categoryId2count[$id]);
            } else {
                $id2 = $category['id2'];
                if (empty($tree[$id2])) {
                    $tree[$id2] = new Category($id2, $category['name2']);
                }
                $tree[$id2]->children[$id] = new Category($id, $category['name'], $categoryId2count[$id]);
                $tree[$id2]->countProducts += $categoryId2count[$id];
            }
            if (!empty($main)) {
                $tree[0] = new Category(0, 'Каталог', 0, $main);
            }
            // if (!empty($tree)) {
            //     if (20 < count($tree)) {
            //         $other = new Category(-1, 'Прочие');
            //         foreach ($tree as $id2 => $cat2) {
            //             if (1 == count($cat2->children)) {
            //                 $cat2->name .= ' » '.reset($cat2->children)->name;
            //                 $other->children[$cat2->id] = $cat2;
            //             }
            //         }
            //         if (!empty($other->children)) {
            //             $tree[-1] = $other;
            //         }
            //     }
            // }
        }

        return $tree;
    } 
}
