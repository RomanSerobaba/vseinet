<?php 

namespace AppBundle\Bus\Catalog\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetRandomImagesQueryHandler extends MessageHandler
{
    public function handle(GetRandomImagesQuery $query)
    {
        $em = $this->getDoctrine()->getManager();
        
        $stmt = $em->getConnection()->prepare("
            WITH RECURSIVE r AS (
                
            )


            WITH RECURSIVE r AS (
  WITH b AS (
    SELECT
    min(t.id),
    (
      SELECT t.id
      FROM ttbl AS t
      WHERE
        t.id NOT IN (1, 3, 10, 89, 99, 22, 24, 25, 28, 30)
        AND t.is_active
      ORDER BY t.id DESC
      LIMIT 1
      OFFSET 5 - 1
    ) max
    FROM ttbl AS t
    WHERE 
      t.id NOT IN (1, 3, 10, 89, 99, 22, 24, 25, 28, 30)
      AND t.is_active
  )
  (
    SELECT
      id, min, max, array[]::integer[] || id AS a, 0 AS n
    FROM ttbl AS t, b
    WHERE
      id >= min + ((max - min) * random())::int AND
      t.id NOT IN (1, 3, 10, 89, 99, 22, 24, 25, 28, 30) AND
      t.is_active
    LIMIT 1
  ) UNION ALL (
    SELECT t.id, min, max, a || t.id, r.n + 1 AS n
    FROM ttbl AS t, r
    WHERE
      t.id > min + ((max - min) * random())::int AND
      t.id <> all( a ) AND
      r.n + 1 < 5 AND
      t.id NOT IN (1, 3, 10, 89, 99, 22, 24, 25, 28, 30) AND
      t.is_active
    LIMIT 1
  )
)
SELECT t.* FROM ttbl AS t, r WHERE r.id = t.id

SELECT
                p.base_product_id,
                bpi.basename
            FROM category_path cp
            INNER JOIN base_product bp ON bp.category_id = cp.item_id
            INNER JOIN base_product_image bpi ON bpi.base_product_id = bp.id
            INNER JOIN product p ON p.base_product_id = bp.id AND p.owner_id = :owner_id:
            INNER JOIN (SELECT ROUND(RAND() * (
                SELECT MAX(p.id)
                FROM category_path cp
                INNER JOIN base_product bp ON bp.category_id = cp.item_id
                INNER JOIN base_product_image bpi ON bpi.base_product_id = bp.id
                INNER JOIN product p ON p.base_product_id = bp.id AND p.owner_id = :owner_id:
                WHERE cp.pid = :category_id:
                    AND p.is_active > 0
                    AND IF(:brand_id:, bp.brand_id = :brand_id:, 1)
                    AND bpi.priority = 1
            )) AS id) AS x
            WHERE cp.pid = :category_id:
                AND p.is_active > 0
                AND IF(:brand_id:, bp.brand_id = :brand_id:, 1)
                AND bpi.priority = 1
                AND p.id >= x.id
            ORDER BY p.id
            LIMIT 1

        ");
    }
}