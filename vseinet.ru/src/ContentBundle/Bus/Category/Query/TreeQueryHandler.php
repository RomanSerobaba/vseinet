<?php 

namespace ContentBundle\Bus\Category\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;
use Doctrine\ORM\Query\ResultSetMapping;

class TreeQueryHandler extends MessageHandler
{
    public function handle(TreeQuery $query)
    {   
        $tTree = "
            select distinct
                сp.id,
                cc.name,
                array(select pid from category_path ca where ca.id = сp.id order by plevel) as category_path
            from
                category_path сp
            left join category cc on
                cc.id = сp.id
            where сp.level <= :deep
            order by array(select pid from category_path ca where ca.id = сp.id order by plevel)
        ";

        $rTree = new ResultSetMapping();
        $rTree->addScalarResult('id', 'id', 'integer');
        $rTree->addScalarResult('name', 'name', 'string');
        $rTree->addScalarResult('category_path', 'categoryPath', 'string');

        $em = $this->getDoctrine()->getManager();
        $qTree = $em->createNativeQuery($tTree, $rTree)->setParameters([
            'deep' => $query->deep
        ]);

        $results = $qTree->getArrayResult();

        $arrayOfCategories = [];
        foreach ($results as $value) {
            $value['categoryPathAsArray'] = explode(',', substr($value['categoryPath'], 1 ,-1));
            
            if (!isset($arrayOfCategories[$value['categoryPath']])) {
                $arrayOfCategories[$value['categoryPath']] = new DTO\CategoryTree();
                $arrayOfCategories[$value['categoryPath']]->id = $value['id'];
                $arrayOfCategories[$value['categoryPath']]->name =  $value['name'];
                // Найти родителя
                if (1 < count($value['categoryPathAsArray'])) {
                    $arrayOfCategories['{'. implode(',', array_slice($value['categoryPathAsArray'], 0, -1)) .'}']->categoryes[] = 
                            &$arrayOfCategories[$value['categoryPath']];
                }
            }
            
        }
        
        if (0 == count($arrayOfCategories)) {
           throw new NotFoundHttpException('Структура каталога не обнаружена');
        }
        
        return array_shift($arrayOfCategories);
    }
}