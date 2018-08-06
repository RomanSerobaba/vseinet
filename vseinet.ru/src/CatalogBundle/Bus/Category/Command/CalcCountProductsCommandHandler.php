<?php

namespace CatalogBundle\Bus\Category\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\ProductAvailabilityCode;

class CalcCountProductsCommandHandler extends MessageHandler
{
    public function handle(CalcCountProductsCommand $command)
    {
        $connection = $this->getDoctrine()->getManager()->getConnection();

        $stmt = $connection->prepare("
            UPDATE category  
            SET count_products = (
                SELECT COUNT(bp.id)
                FROM base_product AS bp 
                INNER JOIN category AS c ON c.id = bp.category_id 
                INNER JOIN category_path AS cp ON cp.id = c.id 
                WHERE cp.pid = category.id AND bp.supplier_availability_code > :availabilityCode
            )
            WHERE alias_for_id IS NULL
        ");
        $stmt->execute([
            'availabilityCode' => ProductAvailabilityCode::OUT_OF_STOCK,
        ]);

        $stmt = $connection->prepare("
            UPDATE category 
            SET count_products = (
                SELECT c.count_products
                FROM category AS c
                WHERE c.id = category.alias_for_id
            )
            WHERE alias_for_id IS NOT NULL
        ");
        $stmt->execute();
    }
}
