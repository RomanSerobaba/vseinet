<?php 

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ContentBundle\Bus\SupplierProductTransfer\Query;

/**
 * @VIA\Section("Перенос в базу")
 */
class SupplierProductTransferController extends RestController 
{
    /**
     * @VIA\Get(
     *     path="/suppliers/forTransferToDb/",
     *     description="Получение списка поставщиков",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierProductTransfer\Query\GetSuppliersQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\SupplierProductTransfer\Query\DTO\Supplier")
     *     }
     * )
     */
    public function getSuppliersAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetSuppliersQuery($request->query->all()), $suppliers);

        return $suppliers;
    } 

    /**
     * @VIA\Get(
     *     path="/supplierCategories/forTransferToDb/",
     *     description="Получение списка категорий товаров поставщика",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierProductTransfer\Query\GetSupplierCategoriesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\SupplierProductTransfer\Query\DTO\SupplierCategory")
     *     }
     * )
     */
    public function getSupplierCategoriesAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetSupplierCategoriesQuery($request->query->all()), $categories);

        return $categories;
    }

    /**
     * @VIA\Get(
     *     path="/supplierProducts/forTransferToDb/",
     *     description="Получение списка товаров поставщика",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierProductTransfer\Query\GetSupplierProductsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\SupplierProductTransfer\Query\DTO\SupplierProduct")
     *     }
     * )
     */
    public function getSupplierProductsAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetSupplierProductsQuery($request->query->all()), $products);

        return $products;
    }

    /**
     * @VIA\Get(
     *     path="/categories/forTransferToDb/", 
     *     description="Получение списка категорий сайта",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierProductTransfer\Query\GetCategoriesQuery")
     *     }
     * )
     * @VIA\Response(
     *      status=200,
     *      properties={
     *          @VIA\Property(type="array", model="ContentBundle\Bus\SupplierProductTransfer\Query\DTO\Category"),
     *      }
     * )
     */
    public function getCategoriesAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetCategoriesQuery($request->query->all()), $categories);            

        return $categories;
    }

    /**
     * @VIA\Get(
     *     path="/baseProducts/forTransferToDb/",
     *     description="Получение списка товаров сайта",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierProductTransfer\Query\GetBaseProductsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\SupplierProductTransfer\Query\DTO\BaseProduct")
     *     }
     * )
     */
    public function getBaseProductsAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetBaseProductsQuery($request->query->all()), $products);

        return $products;
    }

    /**
     * @VIA\Get(
     *     path="/baseProducts/foundResultsForTransferToDb/",
     *     description="Поиск товаров сайта",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\SupplierProductTransfer\Query\SearchBaseProductsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\SupplierProductTransfer\Query\DTO\FoundResults")
     *     }
     * )
     */
    public function searchBaseProductsAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\SearchBaseProductsQuery($request->query->all()), $results);

        return $results;
    }
}