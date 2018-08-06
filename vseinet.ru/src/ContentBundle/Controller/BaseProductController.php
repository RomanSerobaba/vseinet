<?php 

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ContentBundle\Bus\BaseProduct\Query;
use ContentBundle\Bus\BaseProduct\Command;

/**
 * @VIA\Section("Товары")
 */
class BaseProductController extends RestController
{   
    /**
     * @VIA\Get(
     *     path="/baseProducts/{id}/",
     *     requirements={"id"="\d+"},
     *     description="Получение товара",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Query\GetQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ContentBundle\Bus\BaseProduct\Query\DTO\BaseProduct")
     *     }
     * )
     */
    public function getAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]), $product);

        return $product;
    }

    /**
     * @VIA\Get(
     *     path="/baseProducts/foundResults/",
     *     description="Поиск товаров",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Query\SearchQuery")
     *     }
     * ) 
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\BaseProduct\Query\DTO\FoundResult")
     *     }
     * )   
     */
    public function searchAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\SearchQuery($request->query->all()), $results);

        return $results;
    }

    /**
     * @VIA\Get(
     *     path="/baseProducts/{id}/images/",
     *     description="Получение изображений товара",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Query\GetImagesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Entity\BaseProductImage")
     *     }
     * )
     */
    public function getImagesAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetImagesQuery($request->query->all(), ['id' => $id]), $images);

        return $images;
    }

    /**
     * @VIA\Get(
     *     path="/baseProducts/{id}/mainImage/",
     *     description="Получение основного изображения товара",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Query\GetImagesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Entity\BaseProductImage")
     *     }
     * )
     */
    public function getMainImageAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetImagesQuery($request->query->all(), ['id' => $id]), $images);

        return empty($images) ? null : reset($images);
    }

    /**
     * @VIA\Get(
     *     path="/baseProducts/{id}/detailValues/",
     *     description="Получение значений характеристик товара",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Query\GetDetailValuesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\BaseProduct\Query\DTO\DetailValue")
     *     }
     * )
     */
    public function getDetailValuesAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetDetailValuesQuery(['id' => $id]), $values);

        return $values;
    }

    /**
     * @VIA\Get(
     *     path="/baseProducts/{id}/parserProducts/",
     *     description="Получение привязанных товаров парсера",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Query\GetParserProductsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\BaseProduct\Query\DTO\ParserProduct")
     *     }
     * )
     */
    public function getParserProductsAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetParserProductsQuery(['id' => $id]), $products);

        return $products;
    }

    /**
     * @VIA\Get(
     *     path="/baseProducts/{id}/supplierProducts/",
     *     description="Получение привязанных товаров поставщиков",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Query\GetSupplierProductsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\BaseProduct\Query\DTO\SupplierProduct")
     *     }
     * )
     */
    public function getSupplierProductsAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetSupplierProductsQuery(['id' => $id]), $products);

        return $products;
    }

    /**
     * @VIA\Put(
     *     path="/baseProducts/{id}/",
     *     requirements={"id"="\d+"},
     *     description="Редактирование товара",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Command\UpdateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function editAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdateCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Put(
     *     path="/baseProducts/{id}/name/",
     *     description="Редактирование наименования товара",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Command\SetNameCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function setNameAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetNameCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Put(
     *     path="/baseProducts/{id}/exname/",
     *     description="Редактирование дополнительной информации в наименовании товара",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Command\SetExnameCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function setExnameAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetExnameCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Put(
     *     path="/baseProducts/{id}/detailValue/",
     *     description="Редактирование характеристики товара",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Command\SetDetailValueCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function setDetailValueAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetDetailValueCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Delete(
     *     path="/baseProducts/{id}/detailValue/",
     *     description="Удаление характеристики товара",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Command\DeleteDetailValueCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function removeDetailValueAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeleteDetailValueCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Patch(
     *     path="/baseProducts/{id}/",
     *     description="Объединение товаров сайта",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Command\MergeCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function mergeAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\MergeCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Put(
     *     path="/baseProducts/isHidden/",
     *     description="Показать / скрыть товары сайта",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Command\SetIsHiddenCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function setIsHiddenAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetIsHiddenCommand($request->request->all()));
    }

    /**
     * @VIA\Put(
     *     path="/baseProducts/category/",
     *     description="Перемещение товаров сайта в другую категорию",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Command\MoveCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function moveAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\MoveCommand($request->request->all()));
    }

    /**
     * @VIA\Delete(
     *     path="/baseProducts/",
     *     description="Удаление товаров сайта",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Command\DeleteCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function removeAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand($request->request->all()));
    }

    /**
     * @VIA\Put(
     *     path="/baseProducts/{id}/brand/",
     *     description="Редактирование бренда",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Command\SetBrandCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="brandId", type="integer")
     *     }
     * )
     */
    public function setBrandAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetBrandCommand($request->request->all(), ['id' => $id]));
        $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]), $product);

        return [
            'brandId' => $product->brandId,
        ];
    }

    /**
     * @VIA\Delete(
     *     path="/baseProducts/{id}/brand/",
     *     description="Удаление бренда",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Command\DeleteBrandCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function removeBrandAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\DeleteBrandCommand(['id' => $id]));
    }

    /**
     * @VIA\Put(
     *     path="/baseProducts/{id}/colorComposite/",
     *     description="Редактирование составного цвета",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Command\SetColorCompositeCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="colorCompositeId", type="integer")
     *     }
     * )
     */
    public function setColorCompositeAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetColorCompositeCommand($request->request->all(), ['id' => $id]));
        $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]), $product);

        return [
            'colorCompositeId' => $product->colorCompositeId,
        ];
    }

    /**
     * @VIA\Delete(
     *     path="/baseProducts/{id}/colorComposite/",
     *     description="Удаление составного цвета",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Command\DeleteColorCompositeCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function removeColorCompositeAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\DeleteColorCompositeCommand(['id' => $id]));
    }

    /**
     * @VIA\Put(
     *     path="/baseProducts/{id}/section/",
     *     description="Редактирование раздела категории",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Command\SetCategorySectionCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204,
     * )
     */
    public function setCategorySectionAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetCategorySectionCommand($request->request->all(), ['id' => $id]));
    }    

    /**
     * @VIA\Get(
     *     path="/baseProducts/byBarcode/",
     *     description="Получение товаров по штрихкоду",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Query\GetByBarcodeQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\BaseProduct\Query\DTO\BaseProduct")
     *     }
     * )
     */
    public function getByBarcodeAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetByBarcodeQuery($request->query->all()), $items);

        return $items;
    }

    /**
     * @VIA\Get(
     *     path="/baseProducts/next/",
     *     description="Переход к редактированию следующего товара",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\BaseProduct\Query\GetNextIdQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="integer", name="id")
     *     }
     * )
     */
    public function getNextAction()
    {
        $this->get('query_bus')->handle(new Query\GetNextIdQuery(), $id);

        return [
            'id' => $id,
        ];
    }
}
