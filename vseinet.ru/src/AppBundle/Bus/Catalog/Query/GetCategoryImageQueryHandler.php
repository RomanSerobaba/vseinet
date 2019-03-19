<?php

namespace AppBundle\Bus\Catalog\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Doctrine\ORM\Query\DTORSM;

class GetCategoryImageQueryHandler extends MessageHandler
{
    public function handle(GetCategoryImageQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createNativeQuery('
            SELECT
                bpi.id,
                bpi.basename AS base_src
            FROM base_product AS bp
            INNER JOIN base_product_image AS bpi ON bpi.base_product_id = bp.id AND bpi.sort_order = 1
            WHERE bp.category_id = :category_id AND bpi.id >= (
                SELECT FLOOR(RANDOM() * (MAX(id) - MIN(id))) + MIN(id)
                FROM base_product_image
            )
            ORDER BY bpi.id
            LIMIT 1
        ', new DTORSM(DTO\Image::class, DTORSM::OBJECT_SINGLE));
        $q->setParameter('category_id', $query->categoryId);

        return $q->getResult('DTOHydrator');
    }
}
