<?php 

namespace ContentBundle\Bus\BaseProductBarCode\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetProductsQueryHandler extends MessageHandler
{
    public function handle(GetProductsQuery $query)
    {
            
        $queryText = "
            SELECT 
                NEW ContentBundle\Bus\BaseProductBarCode\Query\DTO\FindByBarcodeResult(
                    pb.goodsPalletId,
                    gp.title,
                    pb.baseProductId,
                    bp.name
                )
            FROM ContentBundle:BaseProductBarCode pb
            LEFT JOIN ContentBundle:BaseProduct bp WITH bp.id = pb.baseProductId
            LEFT JOIN ReservesBundle:GoodsPallet gp WITH gp.id = pb.goodsPalletId
            WHERE
                pb.barCode = :barCode".
            (!empty($query->withOutProducts) ? " and pb.baseProductId is null" : "").
            (!empty($query->withOutPallets) ? " and pb.goodsPalletId is null" : "")."
            ORDER BY pb.barCode, pb.barCodeType, bp.name";

        $queryDB = $this->getDoctrine()->getManager()->
                createQuery($queryText)->
                setParameters([
                    'barCode' => $query->barCode,
                    ]);
        $products = $queryDB->getArrayResult();
            
        if (0 == count($products)) {
            throw new NotFoundHttpException('Неизвестный штрихкод');
        }
        
        return $products;
        
    }

}