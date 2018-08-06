<?php 

namespace ContentBundle\Bus\Category\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;
use Doctrine\ORM\Query\ResultSetMapping;

class TreeNormQueryHandler extends MessageHandler
{
    public function handle(TreeNormQuery $query)
    {   
        $queryText = "
            with";

        if (!empty($query->geoRoomId)) {

            $queryText .= "
                all_low_category as (
                    select
                        bp.category_id,
                        max(grr.delta) as cnt 
                    from goods_reserve_register grr
                    inner join base_product bp on
                        bp.id = grr.base_product_id
                    where
                        geo_room_id = {$query->geoRoomId} and
                        bp.category_id is not null
                    group by
                        bp.category_id
                    having
                        max(grr.delta) <> 0
                ),";

        }
        
        $queryText .= "
                top_levels as (
                    select distinct
                        cp.pid as cid,
                        cp.plevel as level
                    from category_path cp
                    where";

        if (!empty($query->geoRoomId)) {

            $queryText .= "
                        cp.id in (
                            select
                                category_id
                            from all_low_category) and";

        }
        
        $queryText .= "
                        cp.plevel <= {$query->deep}
                    order by
                        cp.plevel, cp.pid
                )

                select
                    tl.cid as id,
                    cat.name,
                    array_to_json(array(
                        select
                            id
                        from category_path
                        where
                            pid = tl.cid and
                            level - tl.level = 1
                            and id in (select cid from top_levels)
                        order by id
                    )) as categories_ids,
                    tl.level
                from top_levels tl
                left join category cat on
                    cat.id = tl.cid
                ";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id', 'integer');
        $rsm->addScalarResult('name', 'name', 'string');
        $rsm->addScalarResult('categories_ids', 'categoriesIds', 'json');

        $em = $this->getDoctrine()->getManager();
        $dbQuery = $em->createNativeQuery($queryText, $rsm);

        return $dbQuery->getResult();

    }
}