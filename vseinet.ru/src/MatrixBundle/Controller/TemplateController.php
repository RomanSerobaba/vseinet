<?php

namespace MatrixBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use MatrixBundle\Bus\Template\Query;
use MatrixBundle\Bus\Template\Command;

/**
 * @VIA\Section("Ассортиментная матрица, шаблоны")
 */
class TemplateController extends Controller
{   
    /**
     * @VIA\Get(
     *     path="/tradeMatrixTemplates/",
     *     description="Получить список шаблонов ассортиментной матрицы",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="MatrixBundle\Bus\Template\Query\GetListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="MatrixBundle\Bus\Template\Query\DTO\Template", type="array")
     *     }
     * )
     */
    public function getListAction()
    {
        $this->get('query_bus')->handle(new Query\GetListQuery(), $templates);

        return $templates;
    }

    /**
     * @VIA\Get(
     *     path="/tradeMatrixTemplates/{id}/",
     *     description="Получить шаблон ассортиментной матрицы",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="MatrixBundle\Bus\Template\Query\GetQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="MatrixBundle\Bus\Template\Query\DTO\Template", type="array")
     *     }
     * )
     */
    public function getAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]), $templates);

        return $templates;
    }
    
    /**
     * @VIA\Post(
     *     path="/tradeMatrixTemplates/",
     *     description="Создать шаблон",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="MatrixBundle\Bus\Template\Command\CreateCommand")
     *     }
     * )
     * @VIA\Response(
     *      status=201,
     *      properties={
     *          @VIA\Property(name="id", type="integer")
     *      }
     * )
     */
    public function createAction(Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\CreateCommand($request->request->all(), ['uuid' => $uuid]));
        
        return [
            'id' => $this->get('uuid.manager')->loadId($uuid),
        ];
    }
    
    /**
     * @VIA\Put(
     *     path="/tradeMatrixTemplates/{id}/",
     *     description="Редактировать шаблон",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="MatrixBundle\Bus\Template\Command\UpdateCommand")
     *     }
     * )
     * @VIA\Response(      
     *     status=204
     * )
     */
    public function updateAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdateCommand($request->request->all(), ['id' => $id]));
    }  

    /**
    * @VIA\Get(
    *     path="/tradeMatrixTemplates/{id}/items/",
    *     description="Получить список товаров шаблона ассортиментной матрицы",
    *     parameters={
    *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
    *         @VIA\Parameter(model="MatrixBundle\Bus\Template\Query\GetItemsQuery")
    *     }
    * )
    * @VIA\Response(
    *     status=200,
    *     properties={
    *         @VIA\Property(model="MatrixBundle\Bus\Template\Query\DTO\Items")
    *     }
    * )
    */
   public function getItemsAction(int $id, Request $request)
   {
       $this->get('query_bus')->handle(new Query\GetItemsQuery($request->query->all(), ['id' => $id]), $items);

       return $items;
   }

   /**
    * @VIA\Put(
    *     path="/tradeMatrixTemplates/{id}/product/",
    *     description="Изменить количество товара в шаблоне",
    *     parameters={
    *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
    *         @VIA\Parameter(model="MatrixBundle\Bus\Template\Command\UpdateProductQuantityCommand")
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
}
