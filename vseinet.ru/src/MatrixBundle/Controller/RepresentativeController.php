<?php

namespace MatrixBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use MatrixBundle\Bus\Representative\Query;
use MatrixBundle\Bus\Representative\Command;

/**
 * @VIA\Section("Ассортиментная матрица, точки")
 */
class RepresentativeController extends Controller
{   
    /**
     * @VIA\Link(
     *     path="/representatives/{id}/tradeMatrixrepresentatives/",
     *     description="Включить шаблон у точки",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="MatrixBundle\Bus\Representative\Command\LinkCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function linkAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\LinkCommand($request->query->all(), ['id' => $id]));
    }

    /**
     * @VIA\Unlink(
     *     path="/representatives/{id}/tradeMatrixrepresentatives/",
     *     description="Отключить шаблон у точки",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="MatrixBundle\Bus\Representative\Command\UnlinkCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function unlinkAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UnlinkCommand($request->query->all(), ['id' => $id]));
    }

    /**
     * @VIA\Put(
     *     path="/representatives/{id}/tradeMatrixLimit/",
     *     description="Изменить лимит у точки",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="MatrixBundle\Bus\Representative\Command\SetMatrixLimitCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function setMatrixLimitAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetMatrixLimitCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Get(
     *     path="/representatives/forTradeMatrix/",
     *     description="Получить список матричных точек со связанными шаблонами",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="MatrixBundle\Bus\Representative\Query\GetListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="MatrixBundle\Bus\Representative\Query\DTO\Representative", type="array")
     *     }
     * )
     */
    public function getListAction()
    {
        $this->get('query_bus')->handle(new Query\GetListQuery(), $representatives);

        return $representatives;
    }
    
    /**
    * @VIA\Get(
    *     path="/representatives/{id}/tradeMatrix/",
    *     description="Получить список товаров в шаблоне точки",
    *     parameters={
    *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
    *         @VIA\Parameter(model="MatrixBundle\Bus\Representative\Query\GetMatrixQuery")
    *     }
    * )
    * @VIA\Response(
    *     status=200,
    *     properties={
    *         @VIA\Property(model="MatrixBundle\Bus\Representative\Query\DTO\Matrix")
    *     }
    * )
    */
   public function getMatrixAction(int $id, Request $request)
   {
       $this->get('query_bus')->handle(new Query\GetMatrixQuery($request->query->all(), ['id' => $id]), $matrix);

       return $matrix;
   }
   
   /**
    * @VIA\Put(
    *     path="/representatives/{id}/tradeMatrixCategories/",
    *     description="Изменить количество товара в категории шаблона точки",
    *     parameters={
    *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
    *         @VIA\Parameter(model="MatrixBundle\Bus\Representative\Command\UpdateCategoryQuantityCommand")
    *     }
    * )
    * @VIA\Response(
    *     status=204
    * )
    */
    public function updateCategoryQuantityAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdateCategoryQuantityCommand($request->request->all(), ['id' => $id]));
    }
    
   /**
    * @VIA\Put(
    *     path="/representatives/{id}/tradeMatrixProducts/",
    *     description="Изменить количество товара в шаблоне точки",
    *     parameters={
    *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
    *         @VIA\Parameter(model="MatrixBundle\Bus\Representative\Command\UpdateProductQuantityCommand")
    *     }
    * )
    * @VIA\Response(
    *     status=204
    * )
    */
   public function updateProductQuantityAction(int $id, Request $request)
   {
       $this->get('command_bus')->handle(new Command\UpdateProductQuantityCommand($request->request->all(), ['id' => $id]));
   }
    
   /**
    * @VIA\Patch(
    *     path="/representatives/{id}/tradeMatrix/",
    *     description="Скопировать матрицу с другой точки",
    *     parameters={
    *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
    *         @VIA\Parameter(model="MatrixBundle\Bus\Representative\Command\CopyMatrixCommand")
    *     }
    * )
    * @VIA\Response(
    *     status=204
    * )
    */
   public function copyMatrixAction(int $id, Request $request)
   {
       $this->get('command_bus')->handle(new Command\CopyMatrixCommand($request->request->all(), ['id' => $id]));
   }
       
    /**
    * @VIA\Get(
    *     path="/representatives/{id}/tradeMatrixProducts/forOrder/",
    *     description="Получить список позиций для заказа на точку",
    *     parameters={
    *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
    *         @VIA\Parameter(model="MatrixBundle\Bus\Representative\Query\GetProductsForOrderQuery")
    *     }
    * )
    * @VIA\Response(
    *     status=200,
    *     properties={
    *         @VIA\Property(model="MatrixBundle\Bus\Representative\Query\DTO\ItemsForOrder")
    *     }
    * )
    */
    public function getProductsForOrderAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetProductsForOrderQuery($request->query->all(), ['id' => $id]), $matrix);
 
        return $matrix;
    }
}
