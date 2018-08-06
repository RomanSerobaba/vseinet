<?php

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use ContentBundle\Bus\Category\Query;
use ContentBundle\Bus\Category\Command;
use AppBundle\Annotation as VIA;

/**
 * @VIA\Section("Категории сайта")
 */
class CategoryController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/categories/foundResults/",
     *     description="Поиск категорий",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Category\Query\SearchQuery")
     *     }
     * )
     * @VIA\Response(
     *      status=200,
     *      properties={
     *          @VIA\Property(type="array", model="ContentBundle\Bus\Category\Query\DTO\SearchResult")
     *      }
     * )
     */
    public function searchAction(Request $request) 
    {
        $this->get('query_bus')->handle(new Query\SearchQuery($request->query->all()), $categories);

        return $categories;
    }

    /**
     * @VIA\Get(
     *     path="/categories/",
     *     description="Нормализованное дерево категорий с фильтром по скраду и выбором глубины",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Category\Query\TreeNormQuery")
     *     }
     * )
     * @VIA\Response(
     *      status=200,
     *      properties={
     *          @VIA\Property(type="array", model="ContentBundle\Bus\Category\Query\DTO\CategoryTreeNorm")
     *      }
     * )
     */
    public function getTreeNormAction(Request $request) 
    {
        $this->get('query_bus')->handle(new Query\TreeNormQuery($request->query->all()), $categoryTreeNorm);

        return $categoryTreeNorm;
    }

    /**
     * @VIA\Get(
     *     path="/categories/tree/",
     *     description="Дерево категорий",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Category\Query\TreeQuery")
     *     }
     * )
     * @VIA\Response(
     *      status=200,
     *      properties={
     *          @VIA\Property(type="array", model="ContentBundle\Bus\Category\Query\DTO\CategoryTree")
     *      }
     * )
     */
    public function getTreeAction(Request $request) 
    {
        $this->get('query_bus')->handle(new Query\TreeQuery($request->query->all()), $categoryTree);

        return $categoryTree;
    }

    /**
     * @VIA\Get(
     *     path="/categories/{id}/",
     *     requirements={"id"="\d+"}, 
     *     description="Получение категории сайта",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Category\Query\GetQuery")
     *     }
     * )
     * @VIA\Response(
     *      status=200,
     *      properties={
     *          @VIA\Property(model="ContentBundle\Bus\Category\Query\DTO\Category")
     *      }
     * )
     */
    public function getAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetQuery($request->query->all(), ['id' => $id]), $category);    

        return $category;
    }

    /**
     * @VIA\Get(
     *     path="/categories/{id}/template/",
     *     description="Получение шаблона характеристик категории",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Category\Query\GetTemplateQuery")
     *     }
     * )
     * @VIA\Response(
     *      status=200,
     *      properties={
     *          @VIA\Property(model="ContentBundle\Bus\Category\Query\DTO\Template")
     *      }
     * )
     */
    public function getTemplateAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetTemplateQuery(['id' => $id]), $template);    

        return $template;
    }

    /**
     * @VIA\Post(
     *     path="/categories/",
     *     description="Создание категории сайта",
     *     parameters={    
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Category\Command\CreateCommand")
     *     }
     * )
     * @VIA\Response(
     *      status=201,
     *      properties={
     *          @VIA\Property(name="id", type="integer")
     *      }
     * )
     */
    public function newAction(Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\CreateCommand($request->request->all(), ['uuid' => $uuid]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid),
        ];
    }

    /**
     * @VIA\Put(
     *     path="/categories/{id}/",
     *     description="Редактирование категории сайта",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Category\Command\UpdateCommand")
     *     }
     * )
     * @VIA\Response(
     *      status=204
     * )
     */
    public function editAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdateCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Put(
     *     path="/categories/{id}/useExname/",
     *     description="Использовать в наименовании товаров расширенное наименование",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Category\Command\SetUseExnameCommand")
     *     }
     * )
     * @VIA\Response(
     *      status=204
     * )
     */
    public function setUseExnameAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetUseExnameCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Put(
     *     path="/categories/{id}/isTplEnabled/",
     *     description="Включить/отключить шаблон",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Category\Command\SetIsTplEnabledCommand")
     *     }
     * )
     * @VIA\Response(
     *      status=204
     * )
     */
    public function setIsTplEnabledAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetIsTplEnabledCommand($request->request->all(), ['id' => $id]));
    } 

    /**
     * @VIA\Put(
     *     path="/categories/{id}/parent/",
     *     description="Перемещение категории сайта",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Category\Command\MoveCommand")
     *     }
     * )
     * @VIA\Response(
     *      status=204
     * )
     */
    public function moveAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\MoveCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Link(
     *     path="/categories/{id}/",
     *     description="Прилинковка категории сайта как псевдонима",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Category\Command\LinkCommand")
     *     }
     * )
     * @VIA\Response(
     *      status=204
     * )
     */
    public function linkAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\LinkCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Unlink(
     *     path="/categories/{id}/",
     *     description="Отлинковка категории сайта",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Category\Command\UnlinkCommand")
     *     }
     * )
     * @VIA\Response(
     *      status=204
     * )
     */
    public function unlinkAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UnlinkCommand($request->request->all(), ['id' => $id]));
    }  

    /**
     * @VIA\Delete(
     *     path="/categories/{id}/", 
     *     description="Удаление категории сайта",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Category\Command\DeleteCommand")
     *     }
     * )
     * @VIA\Response(
     *      status=204
     * )
     */
    public function removeAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand(['id' => $id]));
    } 

    /**
     * @todo
     * @VIA\Patch(
     *     path="/category/{id}/rename/products/",
     *     description="Переименовать товары в категории",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Category\Command\RenameBaseProductsCommand")
     *     }
     * )
     * @VIA\Response(
     *      status=204
     * )
     */
    public function renameProductsAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\RenameBaseProductsCommand(['id' => $id]));
    }

}