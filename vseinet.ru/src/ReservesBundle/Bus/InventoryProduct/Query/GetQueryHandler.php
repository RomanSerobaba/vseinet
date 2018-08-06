<?php 

namespace ReservesBundle\Bus\InventoryProduct\Query;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;
use AppBundle\ORM\Query\DTORSM;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
 
        $queryText = "
            with
            all_count as(
                select 
                    case when ip.inventory_did is null then ic.inventory_did
                        else ip.inventory_did end as inventory_did,
                    case when ip.base_product_id is null then ic.base_product_id
                        else ip.base_product_id end as base_product_id,
                    case when ip.initial_quantity is null then 0
                        else ip.initial_quantity end as initial_quantity,
                    case when ip.purchase_price is null then 0
                        else ip.purchase_price end as purchase_price,
                    case when ip.retail_price is null then 0
                        else ip.retail_price end as retail_price,
                    array_to_json(array(select to_jsonb(pc_select) from (select pc.participant_id as id, case when pc.found_quantity is null then 0 else pc.found_quantity end as count from inventory_product_counter pc where pc.inventory_did = ip.inventory_did and pc.base_product_id = ip.base_product_id) pc_select)) as found_quantity_by_participants,
                    sum(case when ic.found_quantity is null then 0 else ic.found_quantity end) as found_quantity
                from inventory_product ip
                full join inventory_product_counter ic on ic.inventory_did = ip.inventory_did and ic.base_product_id = ip.base_product_id
                where ip.inventory_did = :inventoryDId
                group by
                    case when ip.inventory_did is null then ic.inventory_did
                        else ip.inventory_did end,
                    case when ip.base_product_id is null then ic.base_product_id
                        else ip.base_product_id end,
                    case when ip.initial_quantity is null then 0
                        else ip.initial_quantity end,
                    case when ip.purchase_price is null then 0
                        else ip.purchase_price end,
                    case when ip.retail_price is null then 0
                        else ip.retail_price end,
                    array(select to_jsonb(pc_select) from (select pc.participant_id as id, case when pc.found_quantity is null then 0 else pc.found_quantity end as count from inventory_product_counter pc where pc.inventory_did = ip.inventory_did and pc.base_product_id = ip.base_product_id) pc_select)
                )

            select 
                ac.inventory_did as inventory_id,
                (select pid from category_path where id = bp.category_id and plevel = 1) as category_id_level_1,
                (select pid from category_path where id = bp.category_id and plevel = 2) as category_id_level_2,
                ac.base_product_id as id,
                bp.name as name,
                ac.initial_quantity,
                ac.purchase_price,
                ac.retail_price,
                ac.found_quantity_by_participants,
                ac.found_quantity
            from all_count ac
            left join base_product bp on bp.id = ac.base_product_id";
        
        if (!empty($query->onlyDifferent)) {
            
            $queryText .= "
            where
                ac.found_quantity <> ac.initial_quantity";
            
        }
        
        $queryText .= "
            order by
                (select pid from category_path where id = bp.category_id and plevel = 1),
                (select pid from category_path where id = bp.category_id and plevel = 2),
                bp.name
        ";

        $em = $this->getDoctrine()->getManager();
        
        $dbQuery = $em->createNativeQuery($queryText, new DTORSM(DTO\InventoryProduct::class, DTORSM::ARRAY_INDEX))-> setParameters(['inventoryDId' => $query->inventoryId]);
        $products = $dbQuery->getResult('DTOHydrator');
        
        if (empty($products)) {
            
            $products = [];
            $categories = [];
                    
        } else {
            
            $categories = [
                0 => [
                    'id' => 0,
                    'productsIds' => [],
                    'categoriesIds' => []
            ]];
            foreach ($products as $value) {

                // Добавить категорию в корень
                
                if (!in_array($value->categoryIdLevel1, $categories[0]['categoriesIds'])) {
                    $categories[0]['categoriesIds'][] = $value->categoryIdLevel1;
                }
                
                //
                
                if (empty($value->categoryIdLevel2)) { // Товары без подгруппы                    
                    
                    // Добавить товар в категорию
                    
                    if (!isset($categories[$value->categoryIdLevel1])) {
                        
                        $categories[$value->categoryIdLevel1] = [
                            'id' => $value->categoryIdLevel1,
                            'productsIds' => [],
                            'categoriesIds' => []
                        ];
                        
                    }elseif (!isset($categories[$value->categoryIdLevel1]['productsIds'])) {
                        
                        $categories[$value->categoryIdLevel1]['productsIds'] = [];
                        
                    }
                    
                    $categories[$value->categoryIdLevel1]['productsIds'][] = $value->id;
                    
                }else{
                    
                    // Добавить товар в подкатегорию
                    
                    if (!isset($categories[$value->categoryIdLevel2])) {
                        $categories[$value->categoryIdLevel2] = [
                            'id' => $value->categoryIdLevel2,
                            'productsIds' => [],
                            'categoriesIds' => []
                        ];
                    }
                    
                    $categories[$value->categoryIdLevel2]['productsIds'][] = $value->id;
                    
                    // Добавить подкатегорию в категорию
                    
                    if (!isset($categories[$value->categoryIdLevel1])) {
                        
                        $categories[$value->categoryIdLevel1] = [
                            'id' => $value->categoryIdLevel1,
                            'productsIds' => [],
                            'categoriesIds' => []
                        ];
                        
                    }
                    
                    if (!in_array($value->categoryIdLevel2, $categories[$value->categoryIdLevel1]['categoriesIds'])) {
                        
                        $categories[$value->categoryIdLevel1]['categoriesIds'][] = $value->categoryIdLevel2;
                        
                    }
                    
                }
            }

            $productsIdsText = join(',', array_keys($categories));
            $queryText = "
                select
                    cat.id,
                    cat.name
                from category cat
                where cat.id in ({$productsIdsText});
                ";

            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('id',   'id', 'integer');
            $rsm->addScalarResult('name', 'name', 'string');
                    
            $categoriesNames = $em->createNativeQuery($queryText, $rsm)->getResult();
            
            foreach ($categoriesNames as $value) {
                $categories[$value['id']]['name'] = $value['name'];
            }
            
            $categories = array_values($categories);
            
        }
        
        return new DTO\InventoryProducts($categories, $products);
    }

}
